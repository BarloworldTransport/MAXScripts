#!/usr/bin/php
<?php
require dirname(__FILE__) . '/classes/FileParser.php';
require dirname(__FILE__) . '/classes/MAX_API_Get.php';

/**
 * MAX_Upload_MMS_Order.php
 *
 * @package MAX_Upload_MMS_Order
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
class MAX_Upload_MMS_Order
{
    
    // : Constants
    const DS = DIRECTORY_SEPARATOR;

    const LIVE_URL = "https://login.max.bwtsgroup.com";

    const TEST_URL = "http://max.mobilize.biz";

    const INI_FILE = "app_data.ini";

    const ERROR_P_ARG_REQUIRED = "ERROR: -t objectinstances requires -p switch to be given. See below usage of the command for objectinstances.";

    const FILE_NOT_FOUND = "ERROR: File not found: %s";

    const ERROR_REQ_ARG_NOT_FOUND = "ERROR: The required options was not given using the -t switch. See usage below:";

    const ERROR_INVALID_ARG = "ERROR: The argument given for option `t` is invalid. Please see below:";

    const ERROR_NO_ARG_GIVEN = "ERROR: You did not provide any options using switches and the -t switch options is required. See usage below:";
    
    // : End
    
    // : Variables:
    private static $_usage = array(
        "MAX_Upload_MMS_Order - This script generates an MMS order and can be used to push orders to MAX Live or Test.",
        "",
        "Usage: MAX_Upload_MMS_Order.php -t 'live|test' [-s '[a][d][D]'] [-d '/path/to/folder'] [-p 'true'|'false' [-f '/path/to/file.xml']]",
        "",
        "Required Arguments:",
        "",
        "-m = 'live' | 'test': Specifies the MAX platform to upload the MMS order",
        " live CONSTANT: " . self::LIVE_URL,
        " test CONSTANT: " . self::TEST_URL,
        "",
        "Optional Arguments:",
        "",
        "-s '[a][d][D]': Specifies what states of the order to generate and save to a folder",
        "Default argument is 'aD' if not specfied",
        "",
        "-d '/path/to/folder': Set the location for MMS xml files to be saved too",
        "Default folder is `base folder of script file`/export if not specified",
        "",
        "-p 'true'|'false': Push order to test. If false order are generated only and not pushed",
        "If not specified, the default value is true. By default the INSERT MMS ORDER is automatically uploaded",
        "",
        "-f '/path/to/file.xml': Specify an xml file to be uploaded",
        "",
        "Usage examples:",
        "",
        "Generate Accepted and Delivered MMS order states. Upload Accepted Order to test and save orders to default folder:",
        "MAX_Upload_MMS_Order.php -m 'test' -s 'aD'",
        "",
        "Generate Accepted and Disbanded MMS order states. Upload Accepted Order to test and save orders to specified folder:",
        "MAX_Upload_MMS_Order.php -m 'test' -s 'aD' -d '/dir/to/folder/'",
        "",
        "Uploaded MMS order xml file to MAX Test:",
        "MAX_Upload_MMS_Order.php -m 'test' -f '/path/to/file.xml'",
        "",
        "Generate Accepted MMS order state. Do not upload Accepted Order to test and save orders to default folder:",
        "MAX_Upload_MMS_Order.php -m 'test' -p 'false'",
        ""
    );

    protected $_mode;

    protected $_username;

    protected $_password;

    protected $_proxyip;

    protected $_data = array();

    protected $_datadir;

    protected $_errdir;

    protected $_errors = array();

    protected $_records = array();

    protected $_tmp;

    protected $_file;
    // : End
    
    // : Magic Functions
    /**
     * MAX_Upload_MMS_Order::__construct()
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
                    if (array_key_exists('f', $_options) && ! array_key_exists('p', $_options)) {
                        
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
     * MAX_Upload_MMS_Order::printUsage()
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
        } else 
            if ($_msg && is_array($_msg)) {
                
                // If array loop each item and print each item on its own line
                foreach ($_msg as $_msg_lineitem) {
                    
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
     * MAX_Upload_MMS_Order::addErrorRecord($_errmsg, $_record, $_process)
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
     * MAX_Upload_MMS_Order::ExportToCSV($csvFile, $arr)
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

new MAX_Upload_MMS_Order();