#!/usr/bin/php
<?php
require dirname(__FILE__) . '/FileParser.php';
require dirname(__FILE__) . '/MAX_API_Get.php';

/**
 * MMS_Order_Util.php
 *
 * @package MMS_Order_Util
 * @author Clinton Wright <cwright@bwtrans.co.za>
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
class MMS_Order_Util
{
    
    // : Constants
    const DS = DIRECTORY_SEPARATOR;

    const LIVE_URL = "https://login.max.bwtsgroup.com";

    const TEST_URL = "http://max.mobilize.biz";

    const CONF_FILE = "bwt-config.json";

    const HISTORY_FILE = "bwt-mms-history.json";

    const DEFAULT_FUNC = "create_upload";

    const ENV_VAR = "BWT_CONFIG_PATH";

    const DEFAULT_CUSTOMER_CODE = "MEAF,NCP";

    const DEFAULT_MMS_STATUS = "Accepted,Disbanded,Delivered";

    const ERROR_P_ARG_REQUIRED = "ERROR: -t objectinstances requires -p switch to be given. See below usage of the command for objectinstances.";

    const FILE_NOT_FOUND = "ERROR: File not found: %s";

    const ERROR_REQ_ARG_NOT_FOUND = "ERROR: The required options was not given using the -t switch. See usage below:";

    const ERROR_INVALID_ARG = "ERROR: The argument given for option `t` is invalid. Please see below:";

    const ERROR_NO_ARG_GIVEN = "ERROR: You did not provide any options using switches and the -t switch options is required. See usage below:";

    const QUERY_OBJ = "queueentry";

    const QUERY_DEFAULT = 'payload like "%xml%" AND payload like "%customer%" AND payload like "%Status>%status%" AND queue = "importedtrip" AND error IS NULL';
    
    // : End
    
    // : Variables:
    private static $_usage = array(
        "MMS_Order_Util - This script generates an MMS order and can be used to push orders to MAX Live or Test.",
        "",
        "php MMS_Order_Util.php [option [parameter]]" . "",
        "Options:",
        "",
        "--create-upload : create new mms order templates and upload accepted state xml file to MAX",
        "--create-only : Only generate xml files and do not upload",
        "--upload-xml '/path/to/file.xml': Only upload xml file to MAX. Specify /path/to/file.xml",
        "--create-templates : Delete, if any existing templates, and create new templates for mms orders",
        "",
        "--set-data-path '/path/to/folder': set directory to save mms xml files",
        "--set-config-path '/path/to/file.json' : set path to file to read/write configuration",
        "--get-config-path : Give location of config file",
        "--get-data-path '/path/to/folder': set directory to save mms xml files",
        "--create-config-file '/path/to/config.json' : create a default config file",
        "--set-config 'key:value' : Set a key:value pair in the configuration",
        "--get-config : Return all key:value pairs relating to all available configuration",
        "",
        "EXAMPLE USAGE:",
        "",
        "php MMS_Order_Util.php : Uses default behaviour when no options given (--create-upload is default behaviour)",
        "php MMS_Order_Util.php --upload-xml '/path/to/file.xml' : Only upload specified xml MMS order to test",
        "php MMS_Order_Util.php --get-config : Return available config key:value pairs",
        "php MMS_Order_Util.php --set-config 'default-upload-platform:test' : Set config key:value pair",
        "php MMS_Order_Util.php --set-data-path '/relative/path/to/folder' : Set relative path to folder to save xml MMS order files into",
        ""
    );

    protected $_mode;

    protected $_username;

    protected $_password;

    protected $_proxyip;

    protected $_mms_order = array(
        "Accepted" => array(),
        "Disbanded" => array(),
        "Delivered" => array(),
        "ShipmentNumber" => ""
    );

    protected $_datadir;

    protected $_errdir;

    protected $_errors = array();

    protected $_records = array();

    protected $_tmp;

    protected $_file;

    protected $_default_func;

    protected $_mms_customer_codes = array();

    protected $_mms_status = array();

    protected $_config = array();

    protected $_mms_templates = array();

    protected $_mms_data = array();

    protected $_log = array();
    
    // : End
    
    // : Getters - Begin
    
    /**
     * MMS_Order_Util::get_mms_customer_codes()
     *
     * Return the current value set for $this->_mms_customer_codes
     *
     * @param return: $this->_mms_customer_codes            
     */
    public function get_mms_customer_codes()
    {
        if ($this->_mms_customer_codes && is_array($this->_mms_customer_codes)) {
            return $this->_mms_customer_codes;
        } else {
            return FALSE;
        }
    }

    /**
     * MMS_Order_Util::get_mms_status()
     *
     * Return the current value set for $this->_mms_status
     *
     * @param return: $this->_mms_status            
     */
    public function get_mms_status()
    {
        if ($this->_mms_status && is_array($this->_mms_status)) {
            return $this->_mms_status;
        } else {
            return FALSE;
        }
    }

    /**
     * MMS_Order_Util::get_mms_order($_state)
     *
     * Get currently saved MMS order for specified state
     *
     * @param string: $_state            
     */
    public function get_mms_order($_state)
    {
        $_result = FALSE;
        
        try {
            if (is_string($_state)) {
                switch (strtolower($_state)) {
                    case "accepted":
                        {
                            $_result = $this->_mms_order[ucfirst(strtolower($_state))];
                            break;
                        }
                    case "delivered":
                        {
                            $_result = $this->_mms_order[ucfirst(strtolower($_state))];
                            break;
                        }
                    case "disbanded":
                        {
                            $_result = $this->_mms_order[ucfirst(strtolower($_state))];
                            break;
                        }
                }
            }
        } catch (Exception $e) {
            addErrorRecord($e->getMessage(), implode(",", $_mms_data), __FUNCTION__);
        }
        
        // Finally return the result
        return $_result;
    }
    
    // : Getters - End
    
    // : Setters - Begin
    
    /**
     * MMS_Order_Util::set_mms_order($_state, $_mms_data)
     *
     * Set a new currently selected mms order that has been fetched
     *
     * @param string: $_state            
     * @param array: $_mms_data            
     */
    public function set_mms_order($_state, $_mms_data)
    {
        try {
            if (is_string($_state) && is_array($_mms_data)) {
                switch (strtolower($_state)) {
                    case "accepted":
                        {
                            $this->_mms_order['Accepted'] = $_mms_data;
                            break;
                        }
                    case "delivered":
                        {
                            $this->_mms_order['Delivered'] = $_mms_data;
                            break;
                        }
                    case "disbanded":
                        {
                            $this->_mms_order['Disbanded'] = $_mms_data;
                            break;
                        }
                }
            }
        } catch (Exception $e) {
            addErrorRecord($e->getMessage(), implode(",", $_mms_data), __FUNCTION__);
        }
    }

    /**
     * MMS_Order_Util::set_mms_customer_codes($_customer_codes = NULL)
     *
     * Set MMS customer_codes to be used in the SQL query that will be used to fetch
     * an MMS order
     *
     * @param array: $_customer_codes            
     */
    public function set_mms_customer_codes($_customer_codes = NULL)
    {
        if ($_customer_codes && is_array($_customer_codes)) {
            
            // Set supplied _customer_codes argument as the customer_codes
            $this->_mms_customer_codes = array(
                $_customer_codes
            );
        } else 
            if (isset($this->_config['data']['mms']['customer_codes'])) {
                
                // If no supplied argument then check if config customer_codes exist and use that
                $this->_mms_customer_codes = $this->_config['data']['mms']['customer_codes'];
            } else {
                
                // Else set default fallback value if none specified in the config and or given in method argument
                $this->_mms_customer_codes = explode(',', self::DEFAULT_CUSTOMER_CODE);
            }
    }

    /**
     * MMS_Order_Util::set_mms_status($_status = NULL)
     *
     * Set MMS status values available to be used in the SQL query that can be used to fetch
     * an MMS order
     *
     * @param array: $_status            
     */
    public function set_mms_status($_status = NULL)
    {
        if ($_status && is_array($_status)) {
            
            // Set supplied _status argument as the available status options
            $this->_mms_status = array(
                $_status
            );
        } else 
            if (isset($this->_config['data']['mms']['customer_codes'])) {
                
                // If no supplied argument then check if config customer_codes exist and use that
                $this->_mms_status = $this->_config['data']['mms']['status'];
            } else {
                
                // Else set default fallback value if none specified in the config and or given in method argument
                $this->_mms_status = explode(',', self::DEFAULT_MMS_STATUS);
            }
    }
    
    // : Setters - End
    
    // : Magic Functions - Begin
    /**
     * MMS_Order_Util::__construct()
     * Class constructor
     */
    public function __construct()
    {
        // Get arguments passed
        if ($_SERVER['argv']) {
            
            // Get arguments passed to the script
            $_commands = $_SERVER['argv'];
            
            // Load the JSON config data
            $this->add_to_log('Load main JSON config file', self::CONF_FILE);
            $this->_config = $this->load_json_file(self::CONF_FILE);
            
            // Set default function using JSON config data
            $this->set_default_behaviour();
            
            // : Set default values to be used for fetching an MMS order from the DB
            $this->set_mms_customer_codes();
            $this->set_mms_status();
            // : End
            
            // Parse the arguments given and perform requested action
            $this->parseArguments($_commands);
            
            // Print log
            if ($this->_log && is_array($this->_log)) {
                foreach ($this->_log as $_key => $_value) {
                    foreach ($_value as $_key_1 => $_value_1) {
                        print("$_key_1: $_value_1" . PHP_EOL);
                    }
                    print(PHP_EOL);
                }
            }
        } else {
            die("FATAL ERROR: This script must be run from a command line. \$_SERVER['argv'] is empty." . PHP_EOL);
        }
    }
    // : Magic Functions - End
    
    // : Public Functions - Begin
    
    /**
     * MMS_Order_Util::create_upload()
     *
     * Fetch MMS order from MAX DB, Create XML File, Upload XML file to TEST platform
     *
     * @param return: $_result            
     */
    public function create_upload()
    {
        $this->add_to_log('Create upload', 'Determined that action to perform is create and upload xml for MMS order');
        
        // Fetch latest MMS orders from MAX Live DB with status Delivered and customer MEAF and save the first matching order
        $this->pick_order();
        
        // Create the XML files for the order
        $this->create_xml_file();
        
        // Check if xml file mms order for accepted state does exist before attempting an upload to Test platform
        if (isset($this->_mms_order['Accepted']['xml_file'])) {
            if (file_exists($this->_mms_order['Accepted']['xml_file'])) {
                
                // Upload the created XML files to the Test platform
                $this->upload_xml_file($this->_mms_order['Accepted']['xml_file'], 'Accepted', $this->get_shipment_number());
            } else {
                $this->addErrorRecord('ERROR: Did not upload MMS. MMS order accepted state xml file not found', $this->_mms_order['Accepted']['xml_file'], __FUNCTION__);
            }
        } else {
            $this->addErrorRecord('ERROR: Did not upload MMS. MMS order accepted state property called xml_file not set', implode(',', $this->_mms_order['Accepted']), __FUNCTION__);
        }
    }

    /**
     * MMS_Order_Util::create_only()
     *
     * Fetch MMS order from MAX DB, Create XML File
     *
     * @param return: $_result            
     */
    public function create_only()
    {
        
        // Fetch latest MMS orders from MAX Live DB with status Delivered and customer MEAF and save the first matching order
        $this->pick_order();
        
        // Create the XML files for the order
        $this->create_xml_file();
    }

    /**
     * MMS_Order_Util::upload_xml()
     *
     * Upload upload XML file only
     *
     * @param return: $_result            
     */
    public function upload_xml()
    {
        $this->upload_xml_file();
    }

    /**
     * MMS_Order_Util::_default()
     *
     * Default script behaviour -> function called herein is the default behaviour
     *
     * @param return: $_result            
     */
    public function _default()
    {
        $_func_name = $this->_default_func;
        $this->$_func_name();
    }

    /**
     * MMS_Order_Util::clear_saved_mms_order()
     *
     * Clear all data for saved mms order
     *
     * @param return: $_result            
     */
    public function clear_saved_mms_order()
    {
        // Reset _mms_order property
        $this->_mms_order = array(
            "Accepted" => array(),
            "Disbanded" => array(),
            "Delivered" => array(),
            "ShipmentNumber" => ""
        );
        
        return TRUE;
    }

    /**
     * MMS_Order_Util::get_last_xml_order()
     * Get filenames of last generated xml order using json file which is created when create_xml was last used
     *
     * @param return: $_result            
     */
    public function get_last_xml_order()
    {
        $_result = FALSE;
        
        // Get history json file full path
        $_filename = preg_replace("@\/|\\\@", self::DS, getenv(self::ENV_VAR)) . SELF::DS . self::HISTORY_FILE;
        
        // Check if config path is defined & shipment is valid & xml file is set and the file exists
        if (file_exists($_filename)) {
            $_data = (array) array();
            
            if (file_exists($_filename)) {
                $_data = $this->load_json_file(self::HISTORY_FILE);
            }
            
            if (count($_data) > 0) {
                // Fetch last record found in array
                $_result = end($_data);
            }
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::get_log()
     *
     * Return script run log
     *
     * @param return: $_result            
     */
    public function get_log()
    {
        $_result = FALSE;
        
        if ($this->_log && is_array($this->_log)) {
            $_result = $this->_log;
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::get_shipment_number()
     *
     * Fetch shipment number of currently saved MMS order
     *
     * @param return: $_result            
     */
    public function get_shipment_number()
    {
        $_result = FALSE;
        
        if ($this->_mms_order['ShipmentNumber'] && is_string($this->_mms_order['ShipmentNumber'])) {
            $_result = $this->_mms_order['ShipmentNumber'];
        }
        
        return $_result;
    }
    
    // : Public Functions - End
    
    // : Private Functions - Begin
    
    /**
     * MMS_Order_Util::printUsage()
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
     * MMS_Order_Util::extract_shipment_no_from_string($_value)
     * Extract the shipment number from the provided string
     *
     * @param string: $_value            
     * @param return: $_result            
     */
    private function extract_shipment_no_from_string($_value)
    {
        $_result = FALSE;
        
        preg_match('@<shipmentnumber>(.*)<\/shipmentnumber>@i', $_value, $_shipmentNo);
        
        $_result = (count($_shipmentNo) > 0 && isset($_shipmentNo[1])) ? $_shipmentNo[1] : FALSE;
        
        return $_result;
    }

    /**
     * MMS_Order_Util::clear_log()
     *
     * Clear the script run log
     */
    private function clear_log()
    {
        if ($this->_log && is_array($this->_log)) {
            $this->_log = array();
        }
    }

    /**
     * MMS_Order_Util::set_mms_shipment_no($_shipmentNo)
     *
     * Set a new shipment for the currently saved MMS order
     *
     * @param array: $_mms_data            
     */
    private function set_mms_shipment_no($_shipmentNo)
    {
        if ($_shipmentNo && is_string($_shipmentNo) && strlen($_shipmentNo) > 1) {
            $this->_mms_order['ShipmentNumber'] = $_shipmentNo;
        }
    }

    /**
     * MMS_Order_Util::parseArguments($_arg)
     * Verify the arguments passed to the script and determine what is required from the script
     * If unable to determine what is desired by looking at arguments or no arguments given, then use default behaviour
     * Else return FALSE and add a new error to the error array property of the class
     *
     * @param string: $_arg            
     * @param integer: $_result            
     */
    private function parseArguments($_arg)
    {
        /**
         * ARGUMENT LIST:
         * ::TASK TO PERFORM
         * --create-upload : create new mms order templates and upload accepted state xml file to MAX
         * --create-only : Only generate xml files and do not upload
         * --upload-xml '/path/to/file.xml': Only upload xml file to MAX. Specify /path/to/file.xml
         * --create-templates : Delete, if any existing templates, and create new templates for mms orders
         *
         * ::CONFIG
         * --set-data-path '/path/to/folder': set directory to save mms xml files.
         * --set-config-path '/path/to/file.json' : set path to file to read/write configuration.
         * --get-config-path : Give location of config file
         * --get-data-path '/path/to/folder': set directory to save mms xml files.
         * --create-config-file '/path/to/config.json' : create a default config file.
         * --set-config 'key:value'
         * --get-config
         */
        if ($_arg && is_array($_arg)) {
            if (count($_arg) >= 1 && count($_arg) <= 3) {
                // Fetch double hyphen specified argument
                $_arg_items = preg_grep("/--/", $_arg);
                
                if (count($_arg_items) == 1) {
                    // : Expected argument count - we expect only 1 argument
                    switch ($_arg_items[1]) {
                        case "--create-only":
                            {
                                $this->create_only();
                                break;
                            }
                        case "--upload-xml":
                            {
                                $this->upload_xml();
                                break;
                            }
                        case "--set-data-path":
                            {
                                break;
                            }
                        case "--set-config-path":
                            {
                                break;
                            }
                        case "--get-data-path":
                            {
                                break;
                            }
                        case "--get-config-path":
                            {
                                break;
                            }
                        case "--create-config-file":
                            {
                                break;
                            }
                        case "--set-config":
                            {
                                break;
                            }
                        case "--get-config":
                            {
                                break;
                            }
                        case "--create-upload":
                            {
                                $this->create_upload();
                                break;
                            }
                        // If option not any of the above options given then print usage
                        default:
                            {
                                $this->printUsage();
                                break;
                            }
                    }
                } else 
                    if (count($_arg_items) >= 2) {
                        // : Cannot have more than 1 option given as an argument => return FALSE and add to error array
                        $this->printUsage('Too many arguments given. Please see usage print out below.');
                    } else 
                        if (count($_arg_items) == 0) {
                            $this->add_to_log(__FUNCTION__, "No arguments provided, using default behaviour: " . $this->_default_func);
                            // : No arguments given => return default task
                            $this->_default();
                        }
            }
        }
    }

    /**
     * MMS_Order_Util::fetch_mms_order()
     * Fetch MMS order from Live
     *
     * @param return: $_result            
     */
    private function fetch_mms_order($_status = 'Delivered', $_shipmentNumber = NULL, $_customer = NULL, $_tenant = "live")
    {
        try {
            // Add step info to log
            $this->add_to_log(__FUNCTION__, "Attempt to fetch orders from MAX $_tenant DB");
            
            // Reset mms_data class property
            $this->_mms_data = array();
            
            // Set default result to FALSE
            $_result = FALSE;
            
            // Determine customer code
            $_customer = $_customer ? $_customer : $this->_mms_customer_codes[0];
            $_status = $_status ? $_status : $this->_mms_status[2];
            
            // Add step info to log
            $this->add_to_log(__FUNCTION__, "status: $_status, ShipmentNumber: $_shipmentNumber, customer: $_customer, tenant: $_tenant");
            
            // Create MAX API Get instance
            $_api = new MAX_API_Get(strtolower($_tenant));
            
            // Set registry object used in get data query
            $_api->setObject(self::QUERY_OBJ);
            
            // Use the default constant query string by replacing the placeholder for the customer_code
            $_filter_str = preg_replace("@%customer%@", "%" . $_customer . "%", self::QUERY_DEFAULT);
            
            // Further amend the query string by replacing placeholder value for status
            $_filter_str = preg_replace("@%status%@", $_status . "%", $_filter_str);
            
            // Check if shipment number provided and insert into filter query
            if ($_shipmentNumber && is_string($_shipmentNumber) && strlen($_shipmentNumber) > 1) {
                $_filter_str .= ' AND payload like "%ShipmentNumber>' . $_shipmentNumber . '%"';
            }
            
            $this->add_to_log(__FUNCTION__, "Filter string: $_filter_str", NULL, __LINE__);
            
            if ($_filter_str && is_string($_filter_str)) {
                // Set the query string to run get data MAX API
                $_api->setFilter($_filter_str);
                
                // Run the query
                $this->add_to_log(__FUNCTION__, "Executing curl session to get data from MAX $_tenant DB via MAX HTTP API");
                $_api->runApiQuery();
                
                // Save the result (array)
                $this->_mms_data = $_api->getData();
                
                // Fetch CuRL logs
                $_curl_logs = $_api->getLogs();
                
                // Fetch CuRL errors
                $_errors = $_api->getErrors();

                // Add CuRL logs to our logs
                if ($_curl_logs && is_array($_curl_logs)) {
                
                    $_log_str = (string) implode(',', $_curl_logs);
                
                    $this->add_to_log(__FUNCTION__, "CURL LOGS: $_log_str" . NULL, __LINE__);
                }                
                
                // Add CuRL error logs to our logs
                if ($_errors && is_array($_errors)) {
                    
                    $_err_str = (string) implode(',', $_errors);
                    
                    $this->add_to_log(__FUNCTION__, "CURL ERRORS: $_err_str" . NULL, __LINE__);
                }
                
                // Remove all escape characters from the returned values
                if (! is_string($this->_mms_data) && $this->_mms_data != "No rows") {
                    $this->clean_mms_data();
                }
                
                $_result = TRUE;
            }
        } catch (Exception $e) {
            addErrorRecord($e->getMessage(), implode(",", $this->_mms_data), __FUNCTION__);
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::prepare_xml_string($_xml_value)
     * Create XML from MMS order values
     *
     * @param string: $_xml_value            
     * @param return: $_result            
     */
    private function prepare_xml_string($_xml_value)
    {
        $_result = FALSE;
        
        if ($_xml_value && is_string($_xml_value)) {
            $_validate_xml = (preg_match("@xml\":@", $_xml_value) && preg_match("@<\?xml\sversion@", $_xml_value) && preg_match("@BWL\.iBIS\.Contracts\.Shipment\.Shipment\.xsd@", $_xml_value) && preg_match("@.*<Shipment.*<\/Shipment>.*@", $_xml_value));
            if ($_validate_xml) {
                $_result = preg_replace('@"}$@', '', $_xml_value);
                $_result = preg_replace('@^xml"\:"@', '', $_result);
            }
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::create_xml_file()
     * Create XML from MMS order values
     *
     * @param return: $_result            
     */
    private function create_xml_file()
    {
        $this->add_to_log(__FUNCTION__, 'Attempting to create xml file for each state of the MMS order');
        $this->add_to_log(__FUNCTION__, implode(',', $this->_mms_order['Accepted']));
        
        if (($this->_mms_order['Accepted'] || $this->_mms_order['Delivered'] || $this->_mms_order['Disbanded']) && $this->get_shipment_number()) {
            foreach ($this->_mms_order as $_key => $_value) {
                if ($_key != "ShipmentNumber" && count($_value) >= 1) {
                    // Save cleaned xml value into MMS order array property
                    $_xml_value = $this->prepare_xml_string($this->_mms_order[$_key]['payload']);
                    $this->_mms_order[$_key]['xml'] = $_xml_value;
                    
                    $_filename = date("Y-m-d") . "_" . $this->get_shipment_number() . "_" . strtolower($_key) . ".xml";
                    $_file = $this->_config['data']['path'] . SELF::DS . $_filename;
                    
                    if ($this->write_to_file($_file, $_xml_value)) {
                        
                        if (file_exists($_file)) {
                            $this->_mms_order[$_key]['xml_file'] = $_file;
                            $this->add_to_xml_history($_key, $_file, $this->get_shipment_number(), 'created');
                        }
                    }
                }
            }
        }
    }

    /**
     * MMS_Order_Util::update_created_xml($_jsondir)
     * Add xml file that has been created to a json file to the config directory path
     *
     * @param return: $_result            
     */
    private function add_to_xml_history($_state, $_xml_file, $_shipmentNo, $_action)
    {
        $_filename = preg_replace("@\/|\\\@", self::DS, getenv(self::ENV_VAR)) . SELF::DS . self::HISTORY_FILE;
        
        // Check if config path is defined & shipment is valid & xml file is set and the file exists
        if ($_action && is_string($_action) && file_exists($_filename) && $_shipmentNo && is_string($_shipmentNo) && strlen($_shipmentNo) > 1 && $_xml_file && is_string($_xml_file) && $_state && is_string($_state)) {
            
            $this->add_to_log(__FUNCTION__, 'Attempting to create xml file for each state of the MMS order');
            $this->add_to_log(__FUNCTION__, "XML history log file: $_filename, state: $_state, XML File: $_xml_file, shipmentNumber: $_shipmentNo, action: $_action");
            
            $_data = (array) array();
            $_record = array(
                $_shipmentNo => array(
                    $_state => array(
                        "xml_file" => $_xml_file,
                        "shipment_no" => $_shipmentNo,
                        "action" => $_action
                    )
                )
            );
            
            if (file_exists($_filename)) {
                $_data = $this->load_json_file(self::HISTORY_FILE);
            }
            
            $_found = FALSE;
            
            if ($_data && is_array($_data)) {
                
                foreach ($_data as $_key => $_value) {
                    if ($_value && is_array($_value)) {
                        if (key($_value) == $_shipmentNo) {
                            $_found = TRUE;
                            
                            $_data[$_key][$_shipmentNo][$_state] = array(
                                "xml_file" => $_xml_file,
                                "shipment_no" => $_shipmentNo,
                                "action" => $_action
                            );
                        }
                    }
                }
            }
            
            if (! $_found) {
                $_data[] = $_record;
            }
            
            if ($_data) {
                $this->write_to_file($_filename, json_encode($_data));
            }
        }
    }

    /**
     * MMS_Order_Util::clear_saved_xml_history()
     * Clear the file containing the history of created xml files
     *
     * @param return: $_result            
     */
    private function clear_saved_xml_history()
    {
        $_result = FALSE;
        
        // Save history file full path
        $_filename = preg_replace("@\/|\\\@", self::DS, getenv(self::ENV_VAR)) . SELF::DS . self::HISTORY_FILE;
        
        $this->add_to_log(__FUNCTION__, "Clear xml history log");
        
        if (file_exists($_filename)) {
            if (unlink($_filename)) {
                $this->add_to_log(__FUNCTION__, "XML history log file deleted. file: $_filename");
                $_result = TRUE;
            }
        }
    }

    /**
     * MMS_Order_Util::upload_xml_file($_xml_file, $_platform = 'test')
     * Upload MMS order XML file to Test
     *
     * @param return: $_result            
     */
    private function upload_xml_file($_xml_file, $_state, $_shipmentNo, $_platform = 'test')
    {
        // Set $_result default value to FALSE
        $_result = FALSE;
        
        if ($_xml_file && is_string($_xml_file) && strlen($_xml_file) > 1 && file_exists($_xml_file) && $_shipmentNo && is_string($_shipmentNo) && strlen($_shipmentNo) > 1 && $_state && is_string($_state)) {
            $this->add_to_log(__FUNCTION__, 'Upload xml file to specified platform');
            $this->add_to_log(__FUNCTION__, "XML File: $_xml_file, state: $_state, shipmentNumber: $_shipmentNo, platform: $_platform");
            
            // Define soap server base url
            $soapServer = $this->_config['maxurl'][$_platform];
            
            try {
                // Define full soap server URL
                $serviceUrl = $soapServer . $this->_config['maxurl']['soap-mms-server'];
                
                $sClient = new SoapClient($serviceUrl, array(
                    'trace' => 1,
                    'exceptions' => 1,
                    'cache_wsdl' => WSDL_CACHE_NONE
                ));
                
                $response = $sClient->addTrip(file_get_contents($_xml_file));
                
                $lastRequestResponse = $sClient->__getLastResponse();
                
                if (preg_match_all('@OK|received|queued@', $response) === 3) {
                    if ($this->is_order_on_test($_shipmentNo)) {
                        $this->add_to_xml_history($_state, $_xml_file, $_shipmentNo, 'uploaded');
                        $_result = TRUE;
                    }
                }
            } catch (SoapFault $e) {
                $this->addErrorRecord($e->getMessage(), $_xml_file, __FUNCTION__);
            }
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::add_to_log($_step, $_msg)
     * Validate MMS data and return the result
     *
     * @param string: $_step            
     * @param string: $_msg            
     * @param return: $_result            
     */
    private function add_to_log($_step, $_msg, $_function = NULL, $_line = NULL)
    {
        $_result = FALSE;
        
        // Validate given arguments
        if ($_step && is_string($_step) && is_string($_msg) && strlen($_msg) > 0) {
            $_log_index = count($this->_log);
            $this->_log[$_log_index]['step'] = $_step;
            $this->_log[$_log_index]['message'] = $_msg;
            $this->_log[$_log_index]['datetime'] = date("Y-m-d H:i:s");
            
            if ($_function) {
                $this->_log[$_log_index]['function'] = $_function;
            }
            
            if ($_line) {
                $this->_log[$_log_index]['line'] = $_line;
            }
            
            $_result = TRUE;
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::validate_mms_data($_mms_data)
     * Validate MMS data and return the result
     *
     * @param array: $_mms_data            
     * @param return: $_result            
     */
    private function validate_mms_data($_mms_data)
    {
        // Some code goes here to validate the mms_data array passed as an argument
    }

    /**
     * MMS_Order_Util::save_order($_mms_order, $_shipmentNumber)
     *
     * Save MMS order multi dimensional array provided in argument as current MMS order
     *
     * @param array: $_mms_order            
     * @param string: $_shipmentNumber            
     * @param return: $_result            
     */
    private function save_order($_mms_order, $_shipmentNumber)
    {
        $_result = FALSE;
        try {
            if ($_mms_order && is_array($_mms_order) && $_shipmentNumber && is_string($_shipmentNumber)) {
                // Reset the saved MMS order so that no old data remains
                $this->clear_saved_mms_order();
                
                foreach ($_mms_order as $_key => $_value) {
                    if ($_value && is_array($_value) && isset($_value['payload'])) {
                        $this->set_mms_order($_key, $_value);
                    }
                }
                
                // Set new shipmentNumber value for saved MMS order
                $this->set_mms_shipment_no($_shipmentNumber);
                
                if ($this->_mms_order['ShipmentNumber'] == $_shipmentNumber) {
                    // If we reach this point then report that order has saved successfully
                    $_result = TRUE;
                }
            }
        } catch (Exception $e) {
            addErrorRecord($e->getMessage(), $_mms_order, __FUNCTION__);
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::is_order_on_test($_shipmentNumber)
     * Using data get MAX API and MMS order shipment number, check if an MMS order exists on
     * the test platform
     *
     * @param return: $_result            
     */
    private function is_order_on_test($_shipmentNumber)
    {
        $_result = FALSE;
        
        $this->fetch_mms_order('Accepted', $_shipmentNumber, NULL, NULL, 'test');
        
        $_debug = strval($this->_mms_data && is_array($this->_mms_data));
        
        $this->add_to_xml_history('Accepted', 'NULL', $_shipmentNumber, $_debug);
        
        if ($this->_mms_data && is_array($this->_mms_data)) {
            if (isset($this->_mms_data[1]['payload'])) {
                $_result = TRUE;
            }
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::pick_order()
     * From the fetched order data, verify that the order does not already exist
     * on the test DB
     *
     * @param return: $_result            
     */
    private function pick_order($_disbanded = FALSE, $_delivered = TRUE)
    {
        $_accepted = TRUE;
        $_initial_order = (string) "Delivered";
        
        $this->add_to_log(__FUNCTION__, 'Find matching order that has required states from fetched MMS order data');
        $this->add_to_log(__FUNCTION__, "Disbanded: $_disbanded, Delivered: $_delivered");
        
        // Fetch MMS order based on value for delivered
        if (! $_delivered && $_disbanded) {
            $_initial_order = "Disbanded";
        } else 
            if (! $_delivered && ! $_disbanded) {
                
                $_initial_order = "Accepted";
            }
        
        $this->fetch_mms_order($_initial_order);
        
        if ($this->_mms_data && is_array($this->_mms_data)) {
            $_shipmentNo = (string) "";
            $_mms_data = $this->_mms_data;
            
            foreach ($_mms_data as $_key => $_value) {
                
                $this->add_to_log(__FUNCTION__, 'Checking instance of fetched MMS order to see if matches');
                
                $_found = FALSE;
                $_mms_accepted = (array) array();
                $_mms_disbanded = (array) array();
                $_mms_delivered = (array) array();
                $_mms_order = $_value;
                
                // Get shipment number from mms record data
                $_shipmentNo = $this->extract_shipment_no_from_string($_mms_order['payload']);
                
                // Verify shipmentNo has a value and is a string as expected
                if ($_shipmentNo && is_string($_shipmentNo)) {
                    if ($_delivered && $_disbanded) {
                        // Save delivered state of fetched MMS order
                        $_mms_delivered = $_mms_order;
                        
                        $this->fetch_mms_order('Accepted', $_shipmentNo);
                        
                        if ($this->_mms_data && is_array($this->_mms_data)) {
                            $_mms_accepted = $this->_mms_data[1];
                        }
                        
                        $this->fetch_mms_order('Disbanded', $_shipmentNo);
                        
                        if ($this->_mms_data && is_array($this->_mms_data)) {
                            $_mms_disbanded = $this->_mms_data[1];
                        }
                        
                        if (($_mms_accepted && is_array($_mms_accepted)) && (($_mms_delivered && is_array($_mms_delivered))) && (($_mms_disbanded && is_array($_mms_disbanded)))) {
                            // Verify shipment numbers for each state of the MMS order all match to the shipment number been looped for this instance
                            $_accept_shipno = $this->extract_shipment_no_from_string($_mms_accepted['payload']);
                            $_deliver_shipno = $this->extract_shipment_no_from_string($_mms_delivered['payload']);
                            $_disband_shipno = $this->extract_shipment_no_from_string($_mms_disbanded['payload']);
                            
                            if ($_accept_shipno == $_shipmentNo && $_deliver_shipno == $_shipmentNo && $_disband_shipno == $_shipmentNo) {
                                // Construct multidimensional array containing all mms order data states
                                $_data = array(
                                    "Accepted" => $_mms_accepted,
                                    "Disbanded" => $_mms_disbanded,
                                    "Delivered" => $_mms_delivered
                                );
                                
                                // Save the order
                                if ($_data && is_array($_data) && is_array($_data['Accepted']) && is_array($_data['Disbanded']) && is_array($_data['Delivered'])) {
                                    // $_found = TRUE if order saves
                                    $_found = $this->save_order($_data, $_shipmentNo);
                                }
                            }
                        }
                    } else 
                        if ($_delivered && ! $_disbanded) {
                            
                            // Save delivered state of fetched MMS order
                            $_mms_delivered = $_mms_order;
                            
                            $this->fetch_mms_order('Accepted', $_shipmentNo);
                            
                            if ($this->_mms_data && is_array($this->_mms_data)) {
                                $_mms_accepted = $this->_mms_data[1];
                            }
                            
                            if (($_mms_accepted && is_array($_mms_accepted)) && (($_mms_delivered && is_array($_mms_delivered)))) {
                                // Verify shipment numbers for each state of the MMS order all match to the shipment number been looped for this instance
                                $_accept_shipno = $this->extract_shipment_no_from_string($_mms_accepted['payload']);
                                $_deliver_shipno = $this->extract_shipment_no_from_string($_mms_delivered['payload']);
                                
                                if ($_accept_shipno == $_shipmentNo && $_deliver_shipno == $_shipmentNo) {
                                    // Construct multidimensional array containing all mms order data states
                                    $_data = array(
                                        "Accepted" => $_mms_accepted,
                                        "Delivered" => $_mms_delivered
                                    );
                                    
                                    // Save the order
                                    if ($_data && is_array($_data) && is_array($_data['Accepted']) && is_array($_data['Delivered'])) {
                                        // $_found = TRUE if order saves
                                        $_found = $this->save_order($_data, $_shipmentNo);
                                    }
                                }
                            }
                        } else 
                            if (! $_delivered && $_disbanded) {
                                
                                // Save delivered state of fetched MMS order
                                $_mms_accepted = $_mms_order;
                                
                                $this->fetch_mms_order('Disbanded', $_shipmentNo);
                                
                                if ($this->_mms_data && is_array($this->_mms_data)) {
                                    $_mms_disbanded = $this->_mms_data[1];
                                }
                                
                                if (($_mms_accepted && is_array($_mms_accepted)) && (($_mms_disbanded && is_array($_mms_disbanded)))) {
                                    // Verify shipment numbers for each state of the MMS order all match to the shipment number been looped for this instance
                                    $_accept_shipno = $this->extract_shipment_no_from_string($_mms_accepted['payload']);
                                    $_disband_shipno = $this->extract_shipment_no_from_string($_mms_disbanded['payload']);
                                    
                                    if ($_accept_shipno == $_shipmentNo && $_disband_shipno == $_shipmentNo) {
                                        // Construct multidimensional array containing all mms order data states
                                        $_data = array(
                                            "Accepted" => $_mms_accepted,
                                            "Disbanded" => $_mms_disbanded
                                        );
                                        
                                        // Save the order
                                        if ($_data && is_array($_data) && is_array($_data['Accepted']) && is_array($_data['Disbanded'])) {
                                            // $_found = TRUE if order saves
                                            $_found = $this->save_order($_data, $_shipmentNo);
                                        }
                                    }
                                }
                            }
                    
                    if ($_shipmentNo && is_string($_shipmentNo) && strlen($_shipmentNo) > 1) {
                        $this->add_to_log(__FUNCTION__, "Check if this order is already on Test: $_shipmentNo");
                        // If this expression is TRUE then MMS order already exists on Test and we need to continue finding another MMS order
                        $_found = $this->is_order_on_test($_shipmentNo) ? FALSE : $_found;
                    }
                    
                    if ($_found === TRUE) {
                        $this->add_to_log(__FUNCTION__, "Matching order found and not on test: shipmentNumber: $_shipmentNo");
                        // Break out of foreach because we have found a matching order
                        break;
                    }
                }
            }
        }
    }

    /**
     * MMS_Order_Util::remove_escaped_characters()
     * Remove all escaped characters with extra backslashes for mms data provided as a string
     * Specifically removes the following cases: '\/' AND '\"'
     * These escape characters are returned in the MAX Get API data
     *
     * @param return: $_result            
     */
    private function remove_escaped_chars($_strValue)
    {
        $_result = FALSE;
        
        if ($_strValue && is_string($_strValue)) {
            $_result = preg_replace('@\\\"@', '"', $_strValue);
            $_result = preg_replace('@\\\/@', '/', $_result);
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::clean_mms_data()
     * From the fetched order data, verify that the order does not already exist
     * on the test DB
     *
     * @param return: $_result            
     */
    private function clean_mms_data()
    {
        // Set result default value to FALSE
        $_result = FALSE;
        
        if ($this->_mms_data && is_array($this->_mms_data)) {
            foreach ($this->_mms_data as $_key => $_value) {
                if (isset($_value['payload'])) {
                    $_clean_str = $this->remove_escaped_chars($_value['payload']);
                    if ($_clean_str && is_string($_clean_str)) {
                        $this->_mms_data[$_key]['payload'] = $_clean_str;
                    }
                    
                    // Check if the escaped backslash character has been removed from the payload value string
                    $_result = preg_match_all('@\\\"|\\\/@', $this->_mms_data[$_key]['payload']) ? FALSE : TRUE;
                }
            }
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::set_default_behaviour()
     * Set default script behaviour when no arguments provided to the script
     * Checks to see if JSON config data present for the setting else uses
     * default constant value defined in this class
     *
     * @param return: $_result            
     */
    private function set_default_behaviour()
    {
        if (isset($this->_config['behaviour']['default'])) {
            $_func_name = $this->_config['behaviour']['default'];
            if ($_func_name && is_string($_func_name) && function_exists($this->_default_func)) {
                $this->_default_func = $_func_name;
            } else {
                $this->_default_func = self::DEFAULT_FUNC;
            }
        } else {
            $this->_default_func = self::DEFAULT_FUNC;
        }
    }

    /**
     * MMS_Order_Util::addErrorRecord($_errmsg, $_record, $_process)
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
        $this->add_to_log($_process, $_errmsg);
    }

    /**
     * MMS_Order_Util::ExportToCSV($csvFile, $arr)
     * From supplied csv file save data into multidimensional array
     *
     * @param string: $csvFile            
     * @param array: $_arr            
     */
    private function ExportToCSV($csvFile, $_arr)
    {
        try {
            $this->add_to_log(__FUNCTION__, "Export data to CSV file: CSV File: csvFile");
            
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

    /**
     * MMS_Order_Util::load_config_file($_file)
     * Load config file containing json data
     *
     * @param return: $_result            
     */
    private function load_json_file($_file)
    {
        // Default _result to FALSE
        $_result = FALSE;
        
        try {
            $_full_config_path = preg_replace("@\/|\\\@", self::DS, getenv(self::ENV_VAR)) . self::DS . $_file;
            
            $this->add_to_log(__FUNCTION__, "Load JSON file: $_full_config_path");
            
            if (file_exists($_full_config_path)) {
                $_json_file = file_get_contents($_full_config_path);
                if ($_json_file) {
                    $_json_data = json_decode($_json_file, true);
                    
                    if ($_json_data && is_array($_json_data)) {
                        $this->add_to_log(__FUNCTION__, 'Loaded JSON file successfully');
                        $_result = $_json_data;
                    }
                }
            }
        } catch (Exception $e) {
            addErrorRecord($e->getMessage(), $_full_config_path, __FUNCTION__);
        }
        
        return $_result;
    }

    /**
     * MMS_Order_Util::print_debug($_msg.
     * $_terminate = NULL)
     * Output debug info and terminate code execution
     */
    private function print_debug($_msg, $_terminate = TRUE)
    {
        print("DEBUG:" . PHP_EOL);
        if (is_array($_msg)) {
            // If _msg is array then run through each key => value pair and print each entry to the screen
            foreach ($_msg as $key => $value) {
                print($key . " => ");
                var_dump($value);
                print(PHP_EOL);
            }
        } else 
            if (is_string($_msg)) {
                print_r($_msg);
                print(PHP_EOL);
            }
        
        if ($_terminate) {
            exit();
        }
    }

    /**
     * MMS_Order_Util::write_to_file($_file, $_data)
     * Save provided data to file
     *
     * @param string: $_file            
     * @param string: $_data            
     * @param return: $_result            
     */
    private function write_to_file($_file, $_data)
    {
        try {
            // Set $_result default value to return to FALSE
            $_result = FALSE;
            
            if ($_file && is_string($_file) && $_data && is_string($_data)) {
                $this->add_to_log(__FUNCTION__, "Attempt to write data to file: $_file");
                
                // Write the data to the file
                file_put_contents($_file, $_data);
                
                if (file_exists($_file)) {
                    $this->add_to_log(__FUNCTION__, "Successful writing data to file: $_file");
                    $_result = TRUE;
                }
            }
        } catch (Exception $e) {
            $_str_data = "FILE: $_file => DATA: $_data";
            addErrorRecord($e->getMessage(), $_str_data, __FUNCTION__);
        }
        
        return $_result;
    }
    
    // : Private Functions - End
}

$_mms_order_util = new MMS_Order_Util();
?>
