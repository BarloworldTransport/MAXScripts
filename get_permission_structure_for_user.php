#!/usr/bin/php
<?php
include_once dirname(__FILE__) . '/classes/PullDataFromMySQLQuery.php';

/**
 * get_permission_structure_for_user.php
 *
 * @package get_permission_structure_for_user
 * @author Clinton Wright <cwright@bwtrans.com>
 * @copyright 2015 onwards Barloworld Transport (Pty) Ltd
 * @license GNU GPL v2.0
 * @link https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 *       This program is free software; you can redistribute it and/or
 *       modify it under the terms of the GNU General Public License
 *       as published by the Free Software Foundation; either version 2
 *       of the License, or (at your option) any later version.
 *      
 *       This program is distributed in the hope that it will be useful,
 *       but WITHOUT ANY WARRANTY; without even the implied warranty of
 *       MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *       GNU General Public License for more details.
 */
class get_permission_structure_for_user
{
    
    // : Constants
    const DS = DIRECTORY_SEPARATOR;

    const LIVE_URL = "https://login.max.bwtsgroup.com";

    const TEST_URL = "http://max.mobilize.biz";

    const INI_FILE = "app_data.ini";
	
	const SH_HEADER = "#!/bin/bash";

    // : MAX clia command to update and delete all groups for user memberships
    
    /*
     * clia deleteUsersMembershipFromAllGroups email=
     * clia addUserToGroups email= groups=<group names comma seperated>
     */
     
    const MAX_CLIA_DEL_USER_GROUPS = "\$clia deleteUsersMembershipFromAllGroups email=%e";
    const MAX_CLIA_ADD_USER_TO_GROUP = "\$clia addUserToGroups email=%e groups=%g";
        
    // : End
    // : MAX clia command to update dataview permissions using dataview id
    const MAX_CLIA_DATAVIEW = "\$clia DataView setPermissionsForId dataViewId=%id primaryOwner='%po' primaryOwnerCrud=%poc groupOwner='%go' groupOwnerCrud=%goc";
    // : End
    // : MAX clia commands to update permissions for objectCrudActionProcess entries
    const MAX_CLIA_OBJ_PROCESS = "\$clia ObjectRegistry setProcessOwners objectRegistry=%ob handle=%hd owner='%on' ownerCrud=Create,Read,Update,Delete group='%gn' groupCrud=%gc";
    
    const MAX_CLIA_OBJ_POG = "\$clia ObjectRegistry setObjectPrimaryOwnerGroup objectRegistry=%o group='%g'";
    const MAX_CLIA_OBJ_PCRUD = "\$clia ObjectRegistry setObjectPrimaryOwnerCrud objectRegistry=%o crud=%c";
    const MAX_CLIA_OBJ_GOG = "\$clia ObjectRegistry setObjectGroupOwnerGroup objectRegistry=%o group='%g'";
    const MAX_CLIA_OBJ_GCRUD = "\$clia ObjectRegistry setObjectGroupOwnerCrud objectRegistry=%o crud=%c";
    // : End

    const FILE_NOT_FOUND = "ERROR: File not found: %s";
    
    const ERROR_REQ_ARG_NOT_FOUND = "ERROR: The required options was not given using the -t switch. See usage below:";
	
	const ERROR_INVALID_ARG = "ERROR: The argument given for option `t` is invalid. Please see below:";
    
    const ERROR_NO_ARG_GIVEN = "ERROR: You did not provide any options using switches and the -t switch options is required. See usage below:";
    // : End
    
    // : Variables:
    private static $_usage = array(
        "get_permission_structure_for_user.php - A script that searches very far and deep to determine accurately a users permissions on MAX.",
        "",
        "Usage: get_permission_structure_for_user.php -m \$mode",
        "",
        "Arguments:",
        "",
        "Required options:",
        "-m: database application to be used: {mysql|runq}",
        "",
        "Example:",
        "",
        "get_permission_structure_for_user.php -m mysql",
        "",
    );
    
    private static $_modes = array(
        "mysql",
		"runq",
    );
	
	private $_sql_queries = array(
		"user_group_role_links" => "select g.name as groupName, pg.name as playedByGroupName 
				from group_role_link as grl 
				left join `group` as g on (g.id=grl.group_id) 
				left join `group` as pg on (pg.id=grl.played_by_group_id) 
				where g.name ='%s';",
		"find_user_by_email" => "select CONCAT(p.first_name, ' ', p.last_name) as fullname, pu.status, pu.personal_group_id 
				from person as p 
				left join permissionuser as pu on (pu.person_id=p.id) 
				where p.email like '%s';"
	);

    protected $_mode;

    protected $_username;

    protected $_password;

    protected $_proxyip;

    protected $_data = array();

    protected $_datadir;

    protected $_script_code = array();

    protected $_errdir;

    protected $_errors = array();
	
	protected $_records = array();

    protected $_tmp;
    // : End
    
    // : Magic Functions
    /**
     * get_permission_structure_for_user::__construct()
     * Class constructor
     */
    public function __construct()
    {
        $_options = getopt("u:m");
        
        if (count($_options) > 0) {
            
            if (array_key_exists('u', $_options)) {
                
                $ini = dirname(realpath(__FILE__)) . self::DS . "config" . self::DS . self::INI_FILE;
                
                if (is_file($ini) === FALSE) {
                    echo "No " . self::INI_FILE . " file found. Please create it and populate it with the following data: username=x@y.com, password=`your password`, your name shown on MAX the welcome page welcome=`Joe Soap` and mode=`test` or `live`" . PHP_EOL;
                    return FALSE;
                }
                $data = parse_ini_file($ini);
                if ((array_key_exists("datadir", $data) && $data["datadir"]) && (array_key_exists("errordir", $data) && $data["errordir"]) && (array_key_exists("username", $data) && $data["username"]) && (array_key_exists("password", $data) && $data["password"]) && (array_key_exists("mode", $data) && $data["mode"]) && (array_key_exists("proxy", $data) && $data["proxy"])) {
                    
                    $this->_username = $data["username"];
                    $this->_password = $data["password"];
                    $this->_mode = $data["mode"];
                    $this->_proxyip = $data["proxy"];
                    $this->_datadir = $data["datadir"];
                    $this->_errdir = $data["errordir"];
                    
                    switch ($this->_mode) {
                        case "live":
                            $this->_maxurl = self::LIVE_URL;
                            break;
                        default:
                            $this->_maxurl = self::TEST_URL;
                    }
                    
					if (array_key_exists('m', $_options)) {
						
					} else {
						
					}
                    
                } else {
                    $this->printUsage("The correct data is not present in" . self::INI_FILE . ". Please confirm you have the following fields present: username, password, proxyip, datadir, errordir and mode");
                }
            } else {
                $this->printUsage(self::ERROR_REQ_ARG_NOT_FOUND);
            }
        } else {
            $this->printUsage(self::ERROR_NO_ARG_GIVEN);     
        }
    }
    // : End
    
    // : Public Functions
    
    // : Private Functions
    /**
     * get_permission_structure_for_user::importData()
     * Prints the usage static property belonging to the class to output the usage of the script from the command line
     */
    private function printUsage($_msg = null)
    {
        // Clear the screen
        system('clear');
        
        // : Print a message before printing the usage is supplied
        if ($_msg && is_string($_msg)) {
            
            // If string print on its own line
            print($_msg . PHP_EOL);
            
        } else if ($_msg && is_array($_msg)) {
            
            // If array loop each item and print each item on its own line
            foreach($_msg as $_msg_lineitem) {
                
                print($_msg_lineitem . PHP_EOL);
                
            }
        }
        // : End
        
        // Print a blank line and then beginning printing message
        print(PHP_EOL);
        
        // : Print usage string line by line
        foreach (self::$_usage as $_lineitem) {
            print($_lineitem . PHP_EOL);
        }
        // To keep things clean print a blank line at the end
        print(PHP_EOL);
        // : End
        
        // Terminate
        exit();
    }
	
    /**
     * get_permission_structure_for_user::get_ocap_permissions_for_user()
     * Check if user exists and if so return data from DB for the user
	 * @param string: $_email
     */	
	private function get_user($_email) {
		if (is_string($_email) && $_email) {
			
		}
	}

    /**
     * get_permission_structure_for_user::get_ocap_permissions_for_user()
     * Get permissions from objectcrudactionprocess for the user 
     */	
	private function get_ocap_permissions_for_user() {
		
	}
	
	/**
     * get_permission_structure_for_user::get_object_registry_permissions_for_user()
     * Get permissions from objectregistry for the user 
     */	
	private function get_object_registry_permissions_for_user() {
		
	}
	
    /**
     * get_permission_structure_for_user::get_tab_permissions_for_user()
     * Get permissions from tab for the user 
     */	
	private function get_tab_permissions_for_user() {
		
	}
	
	/**
     * get_permission_structure_for_user::get_all_groups_user_is_linked_too()
     * Get all groups that the user is linked too
	 * @param string: $_recursive
     */	
	private function get_groups_user_is_linked($_recursive = null) {
		
	}

    /**
     * get_permission_structure_for_user::importData()
     * import data from csv file into array
     */
    private function importData()
    {
        // : Import Data
        $_file = dirname(__FILE__) . self::DS . $this->_datadir . self::DS . $this->_file;
        
        if (file_exists($_file)) {
            $_csvFile = new FileParser($_file);
            $_csvData = $_csvFile->parseFile();
            if ($_csvData) {
                foreach ($_csvData as $key => $value) {
                    if ($key !== 0) {
                        foreach ($value as $childKey => $childValue) {
                            $this->_data[$key][$_csvData[0][$childKey]] = str_ireplace("'", "", $childValue);
                        }
                    }
                }
            }
            if ($_csvData) {
                return $_csvData;
            }
        } else {
            $_errmsg = preg_replace("/%s/", $_file, self::FILE_NOT_FOUND);
            throw new Exception("$_errmsg\n");
        }
        // : End
    }

    /**
     * get_permission_structure_for_user::addErrorRecord($_errmsg, $_record, $_process)
     * Add error record to error array
     *
     * @param string: $_errmsg            
     * @param string: $_record            
     * @param string: $_process            
     */
    private function addErrorRecord($_errmsg, $_record, $_process)
    {
        $_erCount = count($this->_errors);
        $this->_errors[$_erCount + 1]["error"] = $_errmsg;
        $this->_errors[$_erCount + 1]["record"] = $_record;
        $this->_errors[$_erCount + 1]["method"] = $_process;
    }
	
	/**
     * get_permission_structure_for_user::addRecordPass($_msg, $_record, $_process)
     * Add successful record processing to records array
     *
     * @param string: $_msg            
     * @param string: $_record            
     * @param string: $_process            
     */
    private function addRecordPass($_msg, $_record, $_process)
    {
        $_count = count($this->_records);
        $this->_records[$_count + 1]["msg"] = $_msg;
        $this->_records[$_count + 1]["record"] = $_record;
        $this->_records[$_count + 1]["method"] = $_process;
    }

    /**
     * get_permission_structure_for_user::stringHypenFix($_value)
     * Replace long hyphens in string to short hyphens as part of a problem
     * created when importing data from spreadsheets
     *
     * @param string: $_value            
     * @param string: $_result            
     */
    private function stringHypenFix($_value)
    {
        $_result = preg_replace("/–/", "-", $_value);
        return $_result;
    }

    /**
     * get_permission_structure_for_user::ExportToCSV($csvFile, $arr)
     * From supplied csv file save data into multidimensional array
     *
     * @param string: $csvFile            
     * @param array: $_arr            
     */
    private function ExportToCSV($csvFile, $_arr)
    {
        try {
            $_data = (array) array();
            if (file_exists(dirname($csvFile))) {
                $_handle = fopen($csvFile, 'w');
                foreach ($_arr as $key => $value) {
                    fputcsv($_handle, $value);
                }
                fclose($_handle);
            } else {
                $_msg = preg_replace("@%s@", $csvFile, self::DIR_NOT_FOUND);
                throw new Exception($_msg);
            }
        } catch (Exception $e) {
            return FALSE;
        }
    }
    // : End
}

new get_permission_structure_for_user();
?>