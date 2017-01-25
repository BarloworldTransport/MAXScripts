#!/usr/bin/php
<?php
// Error reporting
error_reporting(E_ALL);

// : Includes
include dirname(__FILE__) . '/PullDataFromMySQLQuery.php';
// : End

/**
 * build_user_list_with_no_groups.php
 *
 * @package build_user_list_with_no_groups
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
class build_user_list_with_no_groups
{
    
    // : Constants
    const DS = DIRECTORY_SEPARATOR;

    const CONFIG_FILE = "app_data.ini";

    const SQL_FETCH_GROUP_ROLE_LINKS = 'SELECT gp.name FROM group_role_link AS grl LEFT JOIN `group` AS gp ON (gp.id=grl.group_id) WHERE grl.played_by_group_id=%g;';

    const SQL_FETCH_USERS = 'SELECT CONCAT(p.first_name, " ", p.last_name) AS fullName, p.email, pu.personal_group_id FROM permissionuser AS pu LEFT JOIN person AS p ON (p.id=pu.person_id) WHERE pu.status = 1;';
    
    // : Properties
    protected $_dbname;

    protected $_dbhost;

    protected $_errdir;

    protected $_reportdir;

    protected $_proxyip;

    private static $_columns = array(
        "fullName",
        "email",
        "personal_group_id"
    );
    
    // : Magic Methods
    public function __construct()
    {
        
        // : Parse config file
        $ini = dirname(realpath(__FILE__)) . self::DS . "config" . self::DS . self::CONFIG_FILE;
        
        if (is_file($ini) === FALSE) {
            print("ERROR: Config file not found: " . self::INI_FILE . PHP_EOL . "Please create it and populate it with the following data: proxy='username:password@proxyip:proxyport', errordir='path/from/script/root/dir/to/error/dir/', reportdir='path/from/script/root/dir/to/report/dir/', dbname='database name', dbhost='database server IP'" . PHP_EOL);
            return FALSE;
        }
        
        $data = parse_ini_file($ini);
        
        if ((array_key_exists("reportdir", $data) && $data["reportdir"]) && (array_key_exists("errordir", $data) && $data["errordir"]) && (array_key_exists("dbhost", $data) && $data["dbhost"]) && (array_key_exists("dbname", $data) && $data["dbname"]) && (array_key_exists("proxy", $data) && $data["proxy"])) {
            
            $this->_proxyip = $data["proxy"];
            $this->_reportdir = $data["reportdir"];
            $this->_errdir = $data["errordir"];
            $this->_dbhost = $data["dbhost"];
            $this->_dbname = $data["dbname"];
        }
        
        $_db = new PullDataFromMySQLQuery($this->_dbname, $this->_dbhost);
        
        $_dberr = $_db->getErrors();
        
        if (! $_dberr) {
            
            $_users = (array) array();
            
            // : Fetch list of active users from the DB server
            $_query = self::SQL_FETCH_USERS;
            $_result = $_db->getDataFromQuery($_query);
            
            if ($_result) {
                
                foreach ($_result as $key1 => $value1) {
                    
                    foreach ($value1 as $key2 => $value2) {
                        if ($key2 && $value2) {
                            $_users[$key1][$key2] = $value2;
                        }
                    }
                }
            }
            // : End
            
            // : Fetch groups for each user
            if ($_users) {
                
                foreach ($_users as $key1 => $value1) {
                    
                    $_personal_group_id = $value1['personal_group_id'];
                    $_query = preg_replace("/%g/", $value1['personal_group_id'], self::SQL_FETCH_GROUP_ROLE_LINKS);
                    $_result = $_db->getDataFromQuery($_query);
                    
                    if ($_result) {
                        
                        $_groups_to_discard = array();
                        
                        foreach ($_result as $key2 => $value2) {
                            
                            $i = 0;
                            foreach ($value2 as $key3 => $value3) {
                                
                                $_keep_group = preg_match('/^BU.\-.*/', $value3);
                                if ($_keep_group) {
                                    $_users[$key1]["groups"]["grl_$i"] = $value3;
                                    $i ++;
                                }
                            }
                        }
                    }
                }
            }
            // : End
            // : Using users with their BU groups - seperate users with no BU groups for output
            $_users_with_no_bu = (array) array();
            
            foreach ($_users as $key1 => $value) {
                if (! array_key_exists('groups', $_users[$key1])) {
                    $_users_with_no_bu[$key1] = $_users[$key1];
                }
            }
            
            // : End
            
            // Close database connection
            unset($_db);
            
            // : Build and export a CSV file containing the extracted data
            if ($_users_with_no_bu) {
                try {
                    $_output_file = realpath(dirname(__FILE__)) . self::DS . date("Ymd_His") . "_users_with_no_bu_groups.csv";
                    
                    $fp = fopen($_output_file, "w");
                    
                    fputcsv($fp, self::$_columns);
                    
                    foreach ($_users_with_no_bu as $key1 => $value1) {
                        fputcsv($fp, $value1);
                    }
                    
                    fclose($fp);
                } catch (Exception $e) {
                    print("PHP - FATAL ERROR: Could not write generated script file:" . PHP_EOL);
                    var_dump($e->getMessage());
                }
                
                print("Successfully wrote file to location:" . $_output_file . PHP_EOL);
            }
            // : End
        } else {
            print("ERROR: Failed to connect to DB Server:" . PHP_EOL);
            var_dump($_dberr);
        }
    }

    public function __destruct()
    {
        unset($this);
    }
    
    // : Public Methods
    
    // : Private Methods
}

new build_user_list_with_no_groups();
