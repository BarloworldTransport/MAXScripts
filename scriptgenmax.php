#!/usr/bin/php
<?php
require dirname(__FILE__) . '/classes/FileParser.php';

/**
 * scriptgenmax.php
 *
 * @package scriptgenmax
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
class scriptgenmax
{
    
    // : Constants
    const DS = DIRECTORY_SEPARATOR;

    const LIVE_URL = "https://login.max.bwtsgroup.com";

    const TEST_URL = "http://max.mobilize.biz";

    const INI_FILE = "app_data.ini";

    // : MAX clia command to update and delete all groups for user memberships
    
    /*
     * clia deleteUsersMembershipFromAllGroups email=
     * clia addUserToGroups email= groups=<group names comma seperated>
     */
     
    const MAX_CLIA_DEL_USER_GROUPS = "clia deleteUsersMembershipFromAllGroups email=%e";
    const MAX_CLIA_ADD_USER_TO_GROUP = "clia addUserToGroups email=%e groups=%g";
        
    // : End
    // : MAX clia command to update dataview permissions using dataview id
    const MAX_CLIA_DATAVIEW = "clia DataView setPermissionsForId dataViewId=%id primaryOwner='%po' primaryOwnerCrud=%poc groupOwner='%go' groupOwnerCrud=%goc";
    // : End
    // : MAX clia commands to update permissions for objectCrudActionProcess entries
    const MAX_CLIA_OBJ_PROCESS = "clia ObjectRegistry setProcessOwners objectRegistry=%ob handle=%hd owner='%on' ownerCrud=Create,Read,Update,Delete group='%gn' groupCrud=%gc";
    
    const MAX_CLIA_OBJ_POG = "clia ObjectRegistry setObjectPrimaryOwnerGroup objectRegistry=%o_link group='%g'";
    const MAX_CLIA_OBJ_PCRUD = "clia ObjectRegistry setObjectPrimaryOwnerCrud objectRegistry=%o_link crud=%c";
    const MAX_CLIA_OBJ_GOG = "clia ObjectRegistry setObjectGroupOwnerGroup objectRegistry=%o group='%g'";
    const MAX_CLIA_OBJ_GCRUD = "clia ObjectRegistry setObjectGroupOwnerCrud objectRegistry=%o crud=%c";
    // : End

    const FILE_NOT_FOUND = "ERROR: File not found: %s";
    
    const ERROR_REQ_ARG_NOT_FOUND = "ERROR: The required options was not given using the -t switch. See usage below:";
    
    const ERROR_NO_ARG_GIVEN = "ERROR: You did not provide any options using switches and the -t switch options is required. See usage below:";
    // : End
    
    // : Variables:
    private static $_usage = array(
        "scriptgenmax.php - A script that generates clia calls to update batch records on MAX.",
        "",
        "Usage: scriptgenmax.php -t \$function -f filename",
        "",
        "Arguments:",
        "",
        "Required options:",
        "-t: objectRegistry can be 1 of the following {dataview|objectcrudprocessaction|usermembership}",
        "",
        "Optional options:",
        "-f: csv filename to be used to generate clia commands. The path for this file is specified in app_data.ini config file",
        "",
        "Example:",
        "",
        "scriptgenmax.php -t dataview -f dataview_permissions.csv",
        "",
        "NOTE: If file is not provided in the -f switch then it will be fetched from the ./config/app_data.ini file."
    );
    
    private static $_funcTypes = array(
        "dataview",
        "objectcrudprocessaction",
        "usermembership"
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

    protected $_tmp;

    protected $_file;
    // : End
    
    // : Magic Functions
    /**
     * scriptgenmax::__construct()
     * Class constructor
     */
    public function __construct()
    {
        $_options = getopt("t:f");
        
        if (count($_options) > 0) {
            
            if (array_key_exists('t', $_options)) {
                
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
                    
                    // : If file option is passed at command line using the -f switch the use the supplied filename else use file in app_data.ini config file
                    if (array_key_exists('f', $_options)) {
                        
                        if (is_string($_options["f"])) {
                            
                            $this->_file = $_options["f"];
                            
                        }
                    } else {
                        
                        $this->_file = $data['file1'];
                        
                    }
                    // : End
                    
                    switch ($this->_mode) {
                        case "live":
                            $this->_maxurl = self::LIVE_URL;
                            break;
                        default:
                            $this->_maxurl = self::TEST_URL;
                    }
                    
                    $this->_data = $this->importData();
                    
                    if ($this->_data) {
                        $_script = $this->generateDataViewScriptFile();
                        foreach($_script as $value) {
                            if (is_string($value)) {
                                print($value . PHP_EOL);
                            }
                        }
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
     * scriptgenmax::importData()
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
     * scriptgenmax::generateDataViewScriptFile($_file)
     * Generate dataview script file using csv file data and output to $_file argument
     * $_file will be saved to export_dir specified in config file
     */
    private function generateDataViewScriptFile($_file = null) {
        // clia command API:

        $_script_code = (array) array();
        $_line = (string) "";
        
        foreach ($this->_data as $key => $value) {
            if (array_key_exists('id', $value) && array_key_exists('primaryOwner', $value) && array_key_exists('primaryOwnerCRUD', $value) && array_key_exists('groupOwner', $value) && array_key_exists('groupOwnerCRUD', $value)) {
                $_line = preg_replace("/%poc/", $value['primaryOwnerCRUD'],self::MAX_CLIA_DATAVIEW);
                $_line = preg_replace("/%goc/", $value['groupOwnerCRUD'], $_line);
                $_line = preg_replace("/%po/", $value['primaryOwner'], $_line);
                $_line = preg_replace("/%go/", $value['groupOwner'], $_line);
                if ($_line) {
                    $_script_code[] = $_line;
                }
            } else {
                $this->addErrorRecord("Could not find required columns to import row data", implode(',', $value), __FUNCTION__);
            }
        }
        if ($_script_code) {
            return $_script_code;
        } else {
            return FALSE;
        }
    }
    
    /**
     * scriptgenmax::generateObjectCrudActionProcessScriptFile($_file)
     * Generate objectcrudactionprocess script file using csv file data and output to $_file argument
     * $_file will be saved to export_dir specified in config file
     */
    private function generateObjectCrudActionProcessScriptFile($_file) {
        // clia command API:
        
        $_script_code = (array) array();
        $_line = (string) "";
        
        foreach ($this->_data as $key => $value) {
            if (array_key_exists('id', $value) && array_key_exists('primaryOwner', $value) && array_key_exists('primaryOwnerCRUD', $value) && array_key_exists('groupOwner', $value) && array_key_exists('groupOwnerCRUD', $value)) {
                $_line = preg_replace("/%poc/", $value['primaryOwnerCRUD'],self::MAX_CLIA_DATAVIEW);
                $_line = preg_replace("/%goc/", $value['groupOwnerCRUD'], $_line);
                $_line = preg_replace("/%po/", $value['primaryOwner'], $_line);
                $_line = preg_replace("/%go/", $value['groupOwner'], $_line);
                if ($_line) {
                    $_script_code[] = $_line;
                }
            } else {
                $this->addErrorRecord("Could not find required columns to import row data", implode(',', $value), __FUNCTION__);
            }
        }
        if ($_script_code) {
            return $_script_code;
        } else {
            return FALSE;
        }
    }
    
    /**
     * scriptgenmax::generateUserMembershipScriptFile($_file)
     * Generate usermembership script file using csv file data and output to $_file argument
     * $_file will be saved to export_dir specified in config file
     */
    private function generateUserMembershipScriptFile($_file) {
        /* clia command API:
         *      MAX_CLIA_DEL_USER_GROUPS = "clia deleteUsersMembershipFromAllGroups email=%e";
         *      MAX_CLIA_ADD_USER_TO_GROUP = "clia addUserToGroups email=%e groups=%g";
         */

        $_script_code = (array) array();
        $_line = (string) "";
        
        foreach ($this->_data as $key => $value) {
            if (array_key_exists('id', $value) && array_key_exists('primaryOwner', $value) && array_key_exists('primaryOwnerCRUD', $value) && array_key_exists('groupOwner', $value) && array_key_exists('groupOwnerCRUD', $value)) {
                $_line = preg_replace("/%poc/", $value['primaryOwnerCRUD'],self::MAX_CLIA_DATAVIEW);
                $_line = preg_replace("/%goc/", $value['groupOwnerCRUD'], $_line);
                $_line = preg_replace("/%po/", $value['primaryOwner'], $_line);
                $_line = preg_replace("/%go/", $value['groupOwner'], $_line);
                if ($_line) {
                    $_script_code[] = $_line;
                }
            } else {
                $this->addErrorRecord("Could not find required columns to import row data", implode(',', $value), __FUNCTION__);
            }
        }
        if ($_script_code) {
            return $_script_code;
        } else {
            return FALSE;
        }
    }
    
        /**
     * scriptgenmax::generateObjectCRUDActionProcessScriptFile($_file)
     * Generate ObjectCRUDActionProcess script file using csv file data and output to $_file argument
     * $_file will be saved to export_dir specified in config file
     */
    private function generateObjectCRUDActionProcessScriptFile($_file) {
        // clia command API:

        $_script_code = (array) array();
        $_line = (string) "";
        
        foreach ($this->_data as $key => $value) {
            if (array_key_exists('id', $value) && array_key_exists('primaryOwner', $value) && array_key_exists('primaryOwnerCRUD', $value) && array_key_exists('groupOwner', $value) && array_key_exists('groupOwnerCRUD', $value)) {
                $_line = preg_replace("/%poc/", $value['primaryOwnerCRUD'],self::MAX_CLIA_DATAVIEW);
                $_line = preg_replace("/%goc/", $value['groupOwnerCRUD'], $_line);
                $_line = preg_replace("/%po/", $value['primaryOwner'], $_line);
                $_line = preg_replace("/%go/", $value['groupOwner'], $_line);
                if ($_line) {
                    $_script_code[] = $_line;
                }
            } else {
                $this->addErrorRecord("Could not find required columns to import row data", implode(',', $value), __FUNCTION__);
            }
        }
        if ($_script_code) {
            return $_script_code;
        } else {
            return FALSE;
        }
    }
    
    /**
     * scriptgenmax::importData()
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
     * scriptgenmax::addErrorRecord($_errmsg, $_record, $_process)
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
        $this->_errors[$_erCount + 1]["type"] = $_process;
    }

    /**
     * scriptgenmax::stringHypenFix($_value)
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
     * scriptgenmax::ExportToCSV($csvFile, $arr)
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

new scriptgenmax();
?>