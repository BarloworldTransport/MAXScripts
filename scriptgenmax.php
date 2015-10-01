#!/usr/bin/php
<?php
require dirname(__FILE__) . '/classes/FileParser.php';
require dirname(__FILE__) . '/classes/PullDataFromMySQLQuery.php' ;
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
	const SH_HEADER = "#!/bin/bash";
	
	// : Database Names
	
	const DB_MAX = "max2";
	const DB_INFO_SCHEMA = "information_schema";
	
	// : End
	// : SQL Queries
	
	const SQL_DOES_OBJECT_EXIST = "SELECT id, handle FROM objectregistry WHERE handle LIKE '%s';";
	const SQL_DOES_TABLE_EXIST = "SELECT TABLE_NAME from TABLES WHERE TABLE_SCHEMA like '%db' AND TABLE_NAME like '%t';";
	const SQL_GET_RECORDS_FOR_OBJECT = "SELECT id FROM %t%s ORDER BY id DESC;";
	const SQL_GET_GROUP_FOR_OBJECT = "SELECT id FROM `group` WHERE name like '%s' LIMIT 1;";
	const SQL_GET_PERSONAL_GROUP = "SELECT g.name FROM permissionuser AS pu LEFT JOIN `group` AS g ON (g.id=pu.personal_group_id) WHERE pu.id=%d;";
	const SQL_GET_PERMISSION_USER = "SELECT pu.id FROM person AS p LEFT JOIN permissionuser AS pu ON (pu.person_id=p.id) WHERE p.id=%d;";
	
	// : End
    // : MAX clia command to update and delete all groups for user memberships
     
    const MAX_CLIA_DEL_USER_GROUPS = "\$clia User deleteUsersMembershipFromAllGroups email=%e";
    const MAX_CLIA_ADD_USER_TO_GROUP = "\$clia User addUserToGroups email=%e groups=\"%g\"";
        
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
	// : MAX clia commands to update ownership and permissions for object instances
	
	const MAX_CLIA_OBJ_ASSIGN_PERMISSIONS = "\$clia ObjectRegistry assignPermissions object=%ob 'id=%id' primaryOwner='%po' primaryOwnerCrud='%pc' groupOwnerCrud='%gc' groupOwner='%go'";
	
	// : End
	// : Errors
	
	const ERROR_P_ARG_REQUIRED = "ERROR: -t objectinstances requires -p switch to be given. See below usage of the command for objectinstances.";
	const FILE_NOT_FOUND = "ERROR: File not found: %s";
    const ERROR_REQ_ARG_NOT_FOUND = "ERROR: The required options was not given using the -t switch. See usage below:";
	const ERROR_INVALID_ARG = "ERROR: The argument given for option `t` is invalid. Please see below:";
	const ERROR_NO_ARG_GIVEN = "ERROR: You did not provide any options using switches and the -t switch options is required. See usage below:";
	
	// : End
	
    // : End
    
    // : Variables:
    private static $_usage = array(
        "SCRIPTGENMAX - A script that generates clia commands to update batch records on MAX.",
        "",
        "Usage: scriptgenmax.php -t {function} -f {filename} -p {primaryOwnerGroup,primaryOwnerCRUD,groupOwnerGroup,groupOwnerCRUD}",
        "",
        "Arguments:",
        "",
        "Required options:",
        "-t: objectRegistry can be 1 of the following {dataview|objectregistry|objectcrudprocessaction|usermembership|objectinstances}",
        "",
		"-p: only required if using -t objectinstances option",
		"primaryOwnerGroup = primary owner group on MAX (group must exist)",
		"primaryOwnerCRUD = CRUD (C: Create, R: Read, U: Update, D: Delete) - If not specified default value: CRUD",
		"groupOwnerGroup = group owner group on MAX (group must exist)",
		"groupOwnerCRUD = CRUD (C: Create, R: Read, U: Update, D: Delete) - If not specified default value: R",
		"",
        "Optional options:",
        "-f: csv filename to be used to generate clia commands. The path for this file is specified in app_data.ini config file",
        "",
        "Example:",
        "",
		"Build dataview ownership and permission changes based on csv file data:",
        "scriptgenmax.php -t dataview -f dataview_permissions.csv",
        "",
		"Build clia commands, exported into a .sh file, to change ownership and permissions for all object instance records for",
		"the object given.",
		"",
        "PLEASE NOTE:",
		"",
		"If file is not provided in the -f switch then it will be fetched from the ./config/app_data.ini file.",
		"",
		"If primaryOwnerCRUD and/or groupOwnerCRUD not specified for -p switch, the following defaults are used:",
		"",
		"primaryOwnerCRUD: CRUD",
		"",
		"groupOwnerCRUD: R"
    );
	
	private static $_sh_header_comment = array(
		"##################################################################################",
		"#",
		"# THIS SH FILE WAS GENERATED BY THE scriptgenmax.php PHP SCRIPT",
		"# THIS SCRIPT IS INTENDED TO BE A GENERATED SH FILE FOR RUNNING BATCH COMMANDS",
		"#",
		"##################################################################################",
		"clia=/usr/local/bin/clia"
	);
    
    private static $_funcTypes = array(
        "dataview",
		"objectregisty",
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
	
	protected $_records = array();

    protected $_tmp;

    protected $_file;
	
	protected $_dbmax;
	
	protected $_dbinfoschema;
	// : End
	
    /**
     * scriptgenmax::unabbreviateCRUD($_crudStr)
     * convert abbreviated crud string to full unabbreviated version of the string
	 * i.e. crud = Create, Read, Update, Delete
	 *
	 * @param string: $_crudStr
	 * @return mixed
     */
	public function unabbreviateCRUD($_crudStr) {
			if (is_string($_crudStr) && $_crudStr) {
			$_crudStr = strtolower($_crudStr);
			$_result = (array) array();
			$_count = strlen($_crudStr);
			$_crudResult = False;

			for ($x = 0; $x < $_count; $x++) {
				switch ($_crudStr[$x]) {
					case 'c':
						$_result[] = 'Create';
						break;
					case 'r':
						$_result[] = 'Read';
						break;
					case 'u':
						$_result[] = 'Update';
						break;
					case 'd':
						$_result[] = 'Delete';
						break;
				}
			}

			if ($_result) {
				$_crudResult = implode(',', $_result);
			}

		    // Return result	
			return $_crudResult;
		}
	}

    // : Magic Functions
    /**
     * scriptgenmax::__construct()
     * Class constructor
     */
    public function __construct()
    {
        $_options = getopt("t:f:p:o:");
        
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
					if (array_key_exists('f', $_options) && !array_key_exists('p', $_options)) {
                        
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
                    $_type = strtolower($_options['t']);
					
                    if ($this->_data && $_type != 'objectinstances') {
                        switch ($_type) {
							case "dataview": {
								$_script = $this->generateDataViewScriptFile();	
								break;
							}
							case "objectregistry": {
								$_script = $this->generateObjectRegistryScriptFile();
								break;
							}
							case "objectcrudactionprocess": {
								$_script = $this->generateObjectCrudActionProcessScriptFile();
								break;
							}
							
							case "usermembership": {
								$_script = $this->generateUserMembershipScriptFile();
								break;
							}
							
							default: {
								$this->printUsage(self::ERROR_INVALID_ARG);
								break;
							}
						}
						
                    } else if ($_type == "objectinstances") {
						
						if (array_key_exists('p', $_options) && array_key_exists('o', $_options)) {
							
						$_poptions = explode(',', $_options['p']);
						$_objectregistry = ($_options['o']);
						
						if ($_poptions && is_array($_poptions) && $_objectregistry) {
							
							$_primaryOwner = strtolower($_poptions[0]);
							$_primaryOwnerCRUD = strtolower($_poptions[1]);
							$_groupOwner = strtolower($_poptions[2]);
							$_groupOwnerCRUD = strtolower($_poptions[3]);
							
							$_perms = (array) array();
							
							$_perms['primaryOwner'] = $_primaryOwner;
							$_perms['groupOwner'] = $_groupOwner;
							
							$po_count = count($_primaryOwnerCRUD);
							$go_count = count($_groupOwnerCRUD);
							
							$_permStr = (string) "";
							foreach ($_perms as $key1 => $value1) {
									if ($key1 == 'primaryOwner')
									{
										$value2 =& $_primaryOwnerCRUD;
									} else
									{
										$value2 =& $_groupOwnerCRUD;
									}

									$_permStr = $this->unabbreviateCRUD($value2);
									if ($_permStr) {

										switch ($key1) {
											case 'primaryOwner' :
												$_primaryOwnerCRUD = $_permStr;
												break;
											case 'groupOwner' :
												$_groupOwnerCRUD = $_permStr;
												break;
										}
									}
							}
							
							$this->_dbmax = new PullDataFromMySQLQuery(self::DB_MAX);
							$this->_dbinfoschema = new PullDataFromMySQLQuery(self::DB_INFO_SCHEMA);
						
							if (!$this->_dbmax->getErrors() && !$this->_dbinfoschema->getErrors()) {
							
								// If the connection to database succeeds then run function
								$_script = $this->generateObjectInstanceChangesScript($_objectregistry, $_primaryOwner, $_groupOwner, $_primaryOwnerCRUD, $_groupOwnerCRUD);
							
							} else {
							
								// If there are errors encountered during the connection to the database then exit with error
								system('clear');
								print("Something went wrong. Failed to connect to the MAX and/or information_schema database. Please check PullDataFromMySQLQuery.php for config file and settings." . PHP_EOL);
							}
						}
						
						
						} else {
							print(self::ERROR_P_ARG_REQUIRED . PHP_EOL);
							$this->printUsage();
						}
					}
					
					if (isset($_script)) {
							
						if ($this->_errors) {
							print("There were errors encountered:" . PHP_EOL);
							var_dump($this->_errors);
						}
							
						if ($this->_records) {
							print("Summary of processed records:" . PHP_EOL);
							var_dump($this->_records);
						}
							
						if ($_script) {
							try {
							$_output_file = realpath(dirname(__FILE__)) . self::DS . date("Ymd_His") . "_" . $_options['t'] . ".sh";
							$fp = fopen($_output_file, "w");
							fwrite($fp, self::SH_HEADER . PHP_EOL);
								
							foreach(self::$_sh_header_comment as $_line) {
								fwrite($fp, $_line . PHP_EOL);
							}
							foreach($_script as $value) {
								fwrite($fp, $value . PHP_EOL);
							}
							fclose($fp);
								
							} catch (Exception $e) {
								print("PHP - FATAL ERROR: Could not write generated script file:" . PHP_EOL);
								var_dump($e->getMessage());
							}
								
							print ("Successfully wrote file to location:" . $_output_file . PHP_EOL);
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
     * scriptgenmax::generateDataViewScriptFile($_file = null)
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
				$_line = preg_replace("/%id/", $value['id'], $_line);
				
				$_pass = preg_match_all("/(?:%poc|%goc|%po|%go|%id)/", $_line);
				
                if ($_line && !$_pass) {
                    $_script_code[] = $_line;
					$this->addRecordPass("Adding line item to array was successful", implode(',', $value), __FUNCTION__);
                } else {
					$this->addErrorRecord("Could not add line item to array", implode(',', $value), __FUNCTION__);
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
     * scriptgenmax::generateObjectCrudActionProcessScriptFile($_file = null)
     * Generate objectcrudactionprocess script file using csv file data and output to $_file argument
     * $_file will be saved to export_dir specified in config file
     */
    private function generateObjectCrudActionProcessScriptFile($_file = null) {
        // clia command API:
      
      // clia ObjectRegistry setProcessOwners objectRegistry=%ob handle=%hd owner='%on' ownerCrud=%oc group='%gn' groupCrud=%gc
        
        $_script_code = (array) array();
        $_line = (string) "";
        // array_key_exists('id', $value) && 
        foreach ($this->_data as $key => $value) {
            if (array_key_exists('ocapHandle', $value) && array_key_exists('objectHandle', $value) && array_key_exists('ocaPrimaryOwner', $value) && array_key_exists('ocaPrimaryOwnerCRUD', $value) && array_key_exists('ocaGroupOwner', $value) && array_key_exists('ocaGroupOwnerCRUD', $value)) {
                
				$_pass = 0;
				
				$_line = preg_replace("/%oc/", $value['ocaPrimaryOwnerCRUD'],self::MAX_CLIA_OBJ_PROCESS);
                $_line = preg_replace("/%gc/", $value['ocaGroupOwnerCRUD'], $_line);
                $_line = preg_replace("/%on/", $value['ocaPrimaryOwner'], $_line);
                $_line = preg_replace("/%gn/", $value['ocaGroupOwner'], $_line);
				$_line = preg_replace("/%ob/", $value['objectHandle'], $_line);
				$_line = preg_replace("/%hd/", "'" . $value['ocapHandle'] . "'", $_line);
				
				$_pass = preg_match_all("/(?:%oc|%gc|%on|%gn|%ob|%hd)/", $_line);
				
                if (!$_pass && $_line) {
                    $_script_code[] = $_line;
					$this->addRecordPass("Adding line item to array was successful", implode(',', $value), __FUNCTION__);
                } else {
					$this->addErrorRecord("Failed to add line item to array", implode(',', $value), __FUNCTION__);
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
     * scriptgenmax::generateObjectRegistryScriptFile($_file = null)
     * Generate objectcrudactionprocess script file using csv file data and output to $_file argument
     * $_file will be saved to export_dir specified in config file
     */
    private function generateObjectRegistryScriptFile($_file = null) {
		
        /* clia command API:
		 * 
		 */
        
        $_script_code = (array) array();
		
		$_lines = (array) array(
			"OBJ_POG" => "",
			"OBJ_PCRUD" => "",
			"OBJ_GOG" => "",
			"OBJ_GCRUD" => ""
		);
		
		/*	
			const MAX_CLIA_OBJ_POG = "clia ObjectRegistry setObjectPrimaryOwnerGroup objectRegistry=%o group='%g'";
			const MAX_CLIA_OBJ_PCRUD = "clia ObjectRegistry setObjectPrimaryOwnerCrud objectRegistry=%o crud=%c";
			const MAX_CLIA_OBJ_GOG = "clia ObjectRegistry setObjectGroupOwnerGroup objectRegistry=%o group='%g'";
			const MAX_CLIA_OBJ_GCRUD = "clia ObjectRegistry setObjectGroupOwnerCrud objectRegistry=%o crud=%c";
		*/
		
        foreach ($this->_data as $key => $value) {
            if (array_key_exists('objectHandle', $value) && array_key_exists('objPrimaryOwner', $value) && array_key_exists('objPrimaryOwnerCRUD', $value) && array_key_exists('objGroupOwner', $value) && array_key_exists('objGroupOwnerCRUD', $value)) {
                
				// : Reset array indice values
				$_lines['OBJ_POG'] = "";
				$_lines['OBJ_PCRUD'] = "";
				$_lines['OBJ_GOG'] = "";
				$_lines['OBJ_GCRUD'] = "";
				$_pass = 0;
				// : End

				$_lines['OBJ_POG'] = preg_replace("/%o/", $value['objectHandle'], self::MAX_CLIA_OBJ_POG);
				$_lines['OBJ_POG'] = preg_replace("/%g/", $value['objPrimaryOwner'], $_lines['OBJ_POG']);

                $_lines['OBJ_PCRUD'] = preg_replace("/%o/", $value['objectHandle'], self::MAX_CLIA_OBJ_PCRUD);
				$_lines['OBJ_PCRUD'] = preg_replace("/%c/", $value['objPrimaryOwnerCRUD'], $_lines['OBJ_PCRUD']);
				
				$_lines['OBJ_GOG'] = preg_replace("/%o/", $value['objectHandle'], self::MAX_CLIA_OBJ_GOG);
				$_lines['OBJ_GOG'] = preg_replace("/%g/", $value['objGroupOwner'], $_lines['OBJ_GOG']);
				
				$_lines['OBJ_GCRUD'] = preg_replace("/%o/", $value['objectHandle'], self::MAX_CLIA_OBJ_GCRUD);
				$_lines['OBJ_GCRUD'] = preg_replace("/%c/", $value['objGroupOwnerCRUD'], $_lines['OBJ_GCRUD']);
				
                if ($_lines) {
					foreach($_lines as $_key => $_value) {
						
						$_pass = preg_match_all("/(?:%o|%g|%c)/", $_value);

						if (!$_pass) {
							$_script_code[] = $_value;
							$this->addRecordPass("Adding line item to array was successful", "$_key => $_value", __FUNCTION__);
						} else {
							$this->addErrorRecord("Could not find a value for line item", "$_key => $_value", __FUNCTION__);
						}
					}
                }
				
            } else {
                $this->addErrorRecord("Could not find required columns to import row data", implode(",", $value), __FUNCTION__);
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
    private function generateUserMembershipScriptFile($_file = null) {
        /* clia command API:
         *      MAX_CLIA_DEL_USER_GROUPS = "clia deleteUsersMembershipFromAllGroups email=%e";
         *      MAX_CLIA_ADD_USER_TO_GROUP = "clia addUserToGroups email=%e groups=%g";
         */

        $_script_code = (array) array();
        $_lineDel = (string) "";
		$_lineAdd = (string) "";

        foreach ($this->_data as $key => $value) {
            if (array_key_exists('email', $value) && array_key_exists('groups', $value)) {
                $_lineDel = preg_replace("/%e/", $value['email'],self::MAX_CLIA_DEL_USER_GROUPS);
                $_lineAdd = preg_replace("/%e/", $value['email'], self::MAX_CLIA_ADD_USER_TO_GROUP);
				$_lineAdd = preg_replace("/%g/", $value['groups'], $_lineAdd);
                if ($_lineDel && $_lineAdd) {
                    $_script_code[$key] = "$_lineDel && $_lineAdd";
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
     * scriptgenmax::generateObjectInstanceChangesScript($_file)
     * Generate a script that will make changes to all existing object instance data
	 * on MAX according to the arguments supplied in this function
	 * SQL_GET_PERSONAL_GROUP
	 * SQL_GET_PERMISSION_USER
     */
    private function generateObjectInstanceChangesScript($_objectRegistry, $_primaryOwner, $_groupOwner, $_primaryOwnerCRUD = 'Create,Read,Update,Delete', $_groupOwnerCRUD = 'Read', $_file = null) {
        /* clia command API:
         * 	MAX_CLIA_OBJ_ASSIGN_PERMISSIONS = "\$clia ObjectRegistry assignPermissions object='%ob' 'id=%id' primaryOwner='%po' primaryOwnerCrud='%pc' groupOwnerCrud='%gc' groupOwner='%go'";
         */

        $_script_code = (array) array();
        $_line = (string) "";
		$_table = strtolower($_objectRegistry);
		
		// : Fetch objectRegistry instances on MAX DB
		
		// Build SQL queries
		// SQL_GET_GROUP_FOR_OBJECT
		$_query1 = preg_replace("/%s/", $_table, self::SQL_DOES_OBJECT_EXIST);
		$_query2 = preg_replace("/%db/", self::DB_MAX, self::SQL_DOES_TABLE_EXIST);
		$_query2 = preg_replace("/%t/", $_table, $_query2);
		$_query3 = preg_replace("/%t/", $_table, self::SQL_GET_RECORDS_FOR_OBJECT);
		
		// Do check for object in objectregistry table
		$_objExist = false;
		$_result = $this->_dbmax->getDataFromQuery($_query1);
		if ($_result) {
			if (array_key_exists(0, $_result)) {
				if ($_result[0]['id']) {
					$_objExist = true;
				}
			}
		}
		
		// Do check for table in DB
		$_result = $this->_dbinfoschema->getDataFromQuery($_query2);
		$_tblExist = false;
		
		if ($_result) {
			if (array_key_exists(0, $_result)) {
				if ($_result[0]['TABLE_NAME']) {
					$_tblExist = true;
				}
			}
		}
		
		// Fetch records for object
		//
		$_where = (string) "";
		$_query_x = (string) "";
		$_data = (array) array();

		switch ($_objectRegistry) {
		case "permissionuser":
			$_where = " WHERE status != 'Disabled'";
			$_query_x = preg_replace('/%s/', $_where, self::SQL_GET_RECORDS_FOR_OBJECT);
			$_query_x = preg_replace('/%t/', $_table, $_query_x);
			break;
		case "person":
			$_query_x = "SELECT p.id FROM permissionuser AS pu LEFT JOIN person AS p ON (p.id=pu.person_id) WHERE pu.status != 'Disabled' ORDER BY ID DESC;";
			break;
		default:
			$_query_x = preg_replace('/%s/', '', $_query3);
			break;
		}
		
		$_result = $this->_dbmax->getDataFromQuery($_query_x);
		
		if ($_result && $_tblExist) {
			foreach($_result as $key1 => $value1) {
				if (is_array($value1) && $value1) {
					foreach($value1 as $key2 => $value2) {
						if ($value2) {
							$_data[$key1][$key2] = $value2;
						}
					}
				}
			}
		}
		
		// : End
		
		if ($_data) {
				foreach ($_data as $key1 => $value1) {
				$_pu_id = (int) 0;
				$_pg_name = (string) "";
				$_primaryOwnerFinal = ucwords($_primaryOwner);
				$_groupOwnerFinal = ucwords($_groupOwner);
				$objrname = strtolower($_objectRegistry);
				
				if (($_primaryOwner == "0" || $_groupOwner == "0") && ($objrname == "permissionuser" || $objrname == "person")) {
						
					if ($objrname == "person") {

						$_query_x = preg_replace("/%d/", $value1['id'], self::SQL_GET_PERMISSION_USER);
						$_result = $this->_dbmax->getDataFromQuery($_query_x);
						
						if ($_result) {
							$_pu_id = $_result[0]['id'];
						}
					} else if ($objrname == "permissionuser") {
						$_pu_id = $value1['id'];
					}
					
					if ($_pu_id) {
						$_query_x = preg_replace("/%d/", $_pu_id, self::SQL_GET_PERSONAL_GROUP);
						$_result = $this->_dbmax->getDataFromQuery($_query_x);
								
						if ($_result) {
							$_pg_name = ucwords($_result[0]['name']);
						} 
					}

					if ($_pg_name) {
						$_pg_name = preg_replace("/\'/", "\'", $_pg_name);

						if ($_primaryOwner == "0") {
							$_primaryOwnerFinal = $_pg_name;
						} else {
							$_groupOwnerFinal = $_pg_name;
						}
					}
				}
				if ($_primaryOwnerFinal != '0' && $_groupOwnerFinal != '0') {

					$_line = preg_replace("/%ob/", $_objectRegistry, self::MAX_CLIA_OBJ_ASSIGN_PERMISSIONS);
					$_line = preg_replace("/%id/", $value1['id'], $_line);
					$_line = preg_replace("/%po/", $_primaryOwnerFinal, $_line);
					$_line = preg_replace("/%pc/", $_primaryOwnerCRUD, $_line);
					$_line = preg_replace("/%go/", $_groupOwnerFinal, $_line);
					$_line = preg_replace("/%gc/", $_groupOwnerCRUD, $_line);
				
					if ($_line) {
							$_script_code[$key1] = $_line;
					}
				}
			}
		} else {
				var_dump($_query2);
				exit;
                $this->addErrorRecord("ERROR: No data for object: $_objectRegistry was found. using query: $_query3 for function: " . __FUNCTION__);
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
        $this->_errors[$_erCount + 1]["method"] = $_process;
    }
	
	/**
     * scriptgenmax::addRecordPass($_msg, $_record, $_process)
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
