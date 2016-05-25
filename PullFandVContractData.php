<?php
// Error reporting
error_reporting(E_ALL);

// : Includes

include_once dirname(__FILE__) . DIRECTORY_SEPERATOR . 'classes' . DIRECTORY_SEPERATOR . ' PHPExcel.php';
/**
 * PHPExcel_Writer_Excel2007
 */
include dirname(__FILE__) . DIRECTORY_SEPERATOR . 'classes' . DIRECTORY_SEPERATOR . 'PHPExcel' . DIRECTORY_SEPERATOR . 'Writer' . DIRECTORY_SEPERATOR . 'Excel2007.php';
/**
 * MySQL query pull and return data class
 */
include dirname(__FILE__) . DIRECTORY_SEPARATOR .  'PullDataFromMySQLQuery.php';
// : End

/**
 * PullFandVContractData.php
 *
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class PullFandVContractData
{
    // : Constants
    const DS = DIRECTORY_SEPARATOR;

    const CONFIG_FILE = "bwt-config.json";

    const ENV_VAR = "BWT_CONFIG_PATH";
    
    const ERR_ENV_VAR_NOT_SET = "The environment variable BWT_CONFIG_PATH is not set. Please set it to the config path where config files can be found.";
    
    const ERR_BWT_CONFIG_MAX_DB_NOT_SET = "The key and values for maxdb where not found. Please see the sample_config_file.json file to see what key and values need to be present.";
    
    const SQL_QUERY_FETCH_FANDV_CONTRACTS = "SELECT `fvc`.`id`, `cu`.`tradingName`, `fvc`.`fixedContribution`, `fvc`.`fixedCost`, `fvc`.`numberOfDays`, `bu`.`name` AS `buname`, `fvc`.`startDate`, `fvc`.`endDate`, `td`.`description`, `rat`.`name` AS `rat`, `fvc`.`variableCostRate_id`
FROM `udo_customer` AS `cu` 
LEFT JOIN `udo_fandvcontract` AS `fvc` ON (`fvc`.`customer_id` = `cu`.`id`)
LEFT JOIN `udo_businessunit` AS `bu` ON (`bu`.`id` = `fvc`.`businessUnit_id`)
LEFT JOIN `udo_rates` AS `ra` ON (`ra`.`id` = `fvc`.`variableCostRate_id`)
LEFT JOIN `udo_ratetype` AS `rat` ON (`rat`.`id` = `ra`.`rateType_id`)
LEFT JOIN `udo_truckdescription` AS `td` ON (`td`.`id` = `ra`.`truckDescription_id`)
WHERE `cu`.`active` = 1 AND `cu`.`primaryCustomer` = 1 AND `cu`.`useFandVContract` = 1 AND `fvc`.`startDate` >= '%startDate%' AND `fvc`.`endDate` <= '%stopDate%' ORDER BY `cu`.`tradingName` ASC;";
    
    const SQL_QUERY_FETCH_DRV_VALUES_FOR_CONTRACT = "SELECT `ID`, `type`, `value` FROM `daterangevalue` WHERE `objectInstanceId` = %objinstid% AND `objectregistry_id` = %objregid% AND `beginDate` >= '%startDate%' AND `endDate` <= '%stopDate%';";
    
    const SQL_QUERY_GET_OBJ_UDO_RATES_ID = "SELECT `ID` FROM `objectregistry` WHERE `handle` LIKE 'udo_rates';";
    
    const SQL_QUERY_GET_OBJ_UDO_FANDVCONTRACT_ID = "SELECT `ID` FROM `objectregistry` WHERE `handle` LIKE 'udo_fandvcontract';";
    
    const SQL_QUERY_GET_OBJ_UDO_FANDVCONTRACT_TRUCKLINK_ID = "SELECT `ID` FROM `objectregistry` WHERE `handle` LIKE 'udo_fandvcontracttruck_link';";
    
    const SQL_QUERY_GET_OBJ_UDO_FANDVCONTRACT_ROUTELINK_ID = "SELECT `ID` FROM `objectregistry` WHERE `handle` LIKE 'udo_fandvcontractroute_link';";
    
    const SQL_QUERY_FETCH_FANDV_TRUCK_LINKS = "SELECT `fvctl`.`truck_id`, `tr`.`fleetnum` FROM `udo_fandvcontracttruck_link` AS `fvctl` LEFT JOIN `udo_truck` AS `tr` ON (`tr`.`id` = `fvctl`.`truck_id`) WHERE `fandVContract_id` = %fandvid% ORDER BY `tr`.`fleetnum` ASC;";
    
    const SQL_QUERY_FETCH_FANDV_ROUTE_LINKS = "SELECT `fvcrl`.`route_id`, `fvcrl`.`leadKms`, CONCAT(`lf`.`name`, ' TO ', `lt`.`name`) AS `routeName` FROM `udo_fandvcontractroute_link` AS `fvcrl` LEFT JOIN `udo_route` AS `ro` ON (`ro`.`id` = `fvcrl`.`route_id`) LEFT JOIN `udo_location` AS `lf` ON (`lf`.`id` = `ro`.`locationFrom_id`) LEFT JOIN `udo_location` AS `lt` ON (`lt`.`id` = `ro`.`locationTo_id`) WHERE `fandVContract_id` = %fandvid% ORDER BY `lf`.`name` ASC;";
    
    // : Variables
    protected $_config_file;
    
    protected $_objreg_ids = array(
        'udo_rates' => 496,
        'udo_fandvcontract' => 910,
        'udo_fandvcontracttruck_link' => 911,
        'udo_fandvcontractroute_link' => 992
    );
    
    protected $_maxdb_object;
    
    protected $_errors = array();
    
    protected $_mode;
    
    private static $_usage = array(
        "PullFandVContractData - Pull F&V Contract data for current month and generate XLS file with the data",
        "",
        "php PullFandVContractData.php -m <mode>",
        "Required:",
        "-m <mode>: create|update",
        "",
        "EXAMPLE USAGE:",
        "",
        "Pull F&V contract data from MAX and generate XLS file for rolling over new contracts",
        "php PullFandVContractData.php -m create",
        "",
        "Pull F&V contract data from MAX and generate XLS file for udpating existing contracts",
        "php PullFandVContractData.php -m create",
        ""
    );
    
    // : Getters
    
    /**
     * PullFandVContractData::getErrors()
     * Get errors
     *
     * @param return mixed
     */
    public function getErrors()
    {
        if ($this->_errors) {
            return $this->_errors;
        }
        return FALSE;
    }
    
    // : End
    
    // : Setters
    
    /**
     * PullFandVContractData::setObjectRegIds()
     * Fetch and tet object registry IDs from the MAX database
     *
     * @param return mixed
     */
    public function setObjectRegIds()
    {
    	if ($this->_maxdb_object) {
    		
    		$tmp = $this->_maxdb_object->queryDB(self::SQL_QUERY_GET_OBJ_UDO_RATES_ID);
    		
    		print(PHP_EOL . "Dump to screen the tmp sql query:" . PHP_EOL);
    		var_dump($tmp);
    		//$this->_objreg_ids['udo_rates'];
    		exit();
    		
    	}
    	return FALSE;
    }
    
    // : End
    
    // : Public functions
    /**
     * PullFandVContractData::printErrors()
     * Print the errors to screen if there are any
     *
     * @param return bool
     */
    public function printErrors()
    {
        // : If there is errors then print them to the screen
        if ($this->_errors) {
            if (is_array($this->_errors)) {
                print(PHP_EOL . "Some errors where encountered. See below list of errors:" . PHP_EOL);
        
                foreach($this->_errors as $key => $value) {
                    print("Error #$key:" . PHP_EOL);
        
                    if ($value && is_array($value)) {
        
                        foreach ($value as $key2 => $value2) {
                            print("$key2: $value2" . PHP_EOL);
                        }
                    }
                }
            }
        } else {
            // Print if no errors
            print(PHP_EOL . "No errors." . PHP_EOL);
        }
        return true;
    }
    
    /**
     * PullFandVContractData::writeExcelFile($excelFile, $excelData)
     * Create, Write and Save Excel Spreadsheet from collected data obtained from the variance report
     *
     * @param $excelFile, $excelData            
     */
    public function writeExcelFile($excelFile, $excelData)
    {
        // Create new PHPExcel object
        print("<pre>");
        print(date('H:i:s') . " Create new PHPExcel object" . PHP_EOL);
        $objPHPExcel = new PHPExcel();
        // : End
        
        // : Set properties
        print(date('H:i:s') . " Set properties" . PHP_EOL);
        $objPHPExcel->getProperties()->setCreator("Clinton Wright");
        $objPHPExcel->getProperties()->setLastModifiedBy("Clinton Wright");
        $objPHPExcel->getProperties()->setTitle("title");
        $objPHPExcel->getProperties()->setSubject("subject");
        $objPHPExcel->getProperties()->setDescription("description");
        // : End
        
        // : Setup Workbook Preferences
        print(date('H:i:s') . " Setup workbook preferences" . PHP_EOL);
        $objPHPExcel->getDefaultStyle()
            ->getFont()
            ->setName('Arial');
        $objPHPExcel->getDefaultStyle()
            ->getFont()
            ->setSize(8);
        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setFitToWidth(1);
        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setFitToHeight(0);
        
        // : End
        
        // : Set Column Headers
        $alphaA = range('A', 'Z');
        $alphaVar = range('A', 'Z');
        foreach ($alphaA as $valueA) {
            foreach ($alphaA as $valueB) {
                $alphaVar[] = $valueA . $valueB;
            }
        }
        
        print(date('H:i:s') . " Setup column headers" . PHP_EOL);
        $a = 1;
        $numCol = count($excelData);
        foreach ($excelData as $value1) {
            $aCell = $alphaVar[$a] . "1";
            $objPHPExcel->getActiveSheet()->setCellValue($aCell, $value1["tradingName"]);
            $objPHPExcel->getActiveSheet()
                ->getStyle($aCell)
                ->getFont()
                ->setBold(true);
            $a ++;
        }
        
        // : Set Row Headers
        print(date('H:i:s') . " Setup row headers" . PHP_EOL);
        $rowHeaders = (array) array(
            "Contract",
            "Customer",
            "Contrib",
            "Cost",
            "Days",
            "Rate",
            "Business Unit",
            "Start Date",
            "End Date",
            "Truck Type",
            "Trucks Linked",
            "Routes Linked",
            "RateType",
            "DaysPerMonth",
            "DaysPerTrip",
            "FuelConsumption",
            "FleetValues",
            "ExpectedEmptyKms",
            "ExpectedDistance"
        );
        $a = 1;
        foreach ($rowHeaders as $value) {
            $objPHPExcel->getActiveSheet()
                ->getStyle("A" . strval($a))
                ->getFont()
                ->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue("A" . strval($a), $value);
            $a ++;
        }
        
        // Add more column header value assignments here
        // : End
        
        // : Add data from $excelData array
        print(date('H:i:s') . " Add data from [reportName] report" . PHP_EOL);
        $colCount = (int) 1;
        $objPHPExcel->setActiveSheetIndex(0);
        foreach ($excelData as $value1) {
            foreach ($value1 as $key2 => $value2) {
                if ($value2 != NULL) {
                    $fornum = number_format((intval($value2) / 100), 2, ".", "");
                } else {
                    $fornum = NULL;
                }
                switch ($key2) {
                    case "tradingName":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "2")
                            ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "fixedContribution":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "3")
                            ->setValueExplicit($fornum, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "fixedCost":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "4")
                            ->setValueExplicit($fornum, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "numberOfDays":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "5")
                            ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "rate":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "6")
                            ->setValueExplicit($fornum, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "buname":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "7")
                            ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "startDate":
                        if ($this->_mode != "create") {
                            $objPHPExcel->getActiveSheet()
                                ->getCell($alphaVar[$colCount] . "8")
                                ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                        } else {
                            $objPHPExcel->getActiveSheet()
                                ->getCell($alphaVar[$colCount] . "8")
                                ->setValueExplicit(date("Y-m-01 00:00:00", strtotime("+1 month")), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                        break;
                    case "endDate":
                        if ($this->_mode != "create") {
                            $objPHPExcel->getActiveSheet()
                                ->getCell($alphaVar[$colCount] . "9")
                                ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                        } else {
                            $objPHPExcel->getActiveSheet()
                                ->getCell($alphaVar[$colCount] . "9")
                                ->setValueExplicit(date("Y-m-t 23:59:59", strtotime("+1 month")), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                        break;
                    case "description":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "10")
                            ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "trucks":
                        if (count($value2) != 0) {
                            foreach ($value2 as $value3) {
                                $objPHPExcel->getActiveSheet()
                                    ->getComment($alphaVar[$colCount] . '11')
                                    ->getText()
                                    ->createTextRun($value3);
                                $objPHPExcel->getActiveSheet()
                                    ->getComment($alphaVar[$colCount] . '11')
                                    ->getText()
                                    ->createTextRun("\r\n");
                            }
                            $objPHPExcel->getActiveSheet()
                                ->getCell($alphaVar[$colCount] . "11")
                                ->setValueExplicit("1", PHPExcel_Cell_DataType::TYPE_STRING);
                        } else {
                            $objPHPExcel->getActiveSheet()
                                ->getCell($alphaVar[$colCount] . "11")
                                ->setValueExplicit("0", PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                        break;
                    case "routes":
                        if (count($value2) != 0) {
                            foreach ($value2 as $value3) {
                                $objPHPExcel->getActiveSheet()
                                    ->getComment($alphaVar[$colCount] . '12')
                                    ->getText()
                                    ->createTextRun($value3);
                                $objPHPExcel->getActiveSheet()
                                    ->getComment($alphaVar[$colCount] . '12')
                                    ->getText()
                                    ->createTextRun("\r\n");
                            }
                            $objPHPExcel->getActiveSheet()
                                ->getCell($alphaVar[$colCount] . "12")
                                ->setValueExplicit("1", PHPExcel_Cell_DataType::TYPE_STRING);
                        } else {
                            $objPHPExcel->getActiveSheet()
                                ->getCell($alphaVar[$colCount] . "12")
                                ->setValueExplicit("0", PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                        break;
                    case "rat":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "13")
                            ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "dpm":
                        $_cellvalue = strval(number_format((floatval($fornum) * 100), 0, "", ""));
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "14")
                            ->setValueExplicit($_cellvalue, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "dpt":
                        $_cellvalue = strval(number_format((floatval($fornum) * 100), 0, "", ""));
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "15")
                            ->setValueExplicit($_cellvalue, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "fc":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "16")
                            ->setValueExplicit($fornum, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "fval":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "17")
                            ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "eek":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "18")
                            ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "ed":
                        $objPHPExcel->getActiveSheet()
                            ->getCell($alphaVar[$colCount] . "19")
                            ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                    case "id":
                        if (strtolower($this->_mode) != "create") {
                            $objPHPExcel->getActiveSheet()
                                ->getCell($alphaVar[$colCount] . "20")
                                ->setValueExplicit($value2, PHPExcel_Cell_DataType::TYPE_STRING);
                            break;
                        }
                }
            }
            $colCount ++;
        }
        // : End
        
        // : Setup Column Widths
        
        for ($a = 0; $a >= $numCol; $a ++) {
            $objPHPExcel->getActiveSheet()
                ->getColumnDimension($alphaVar[$a])
                ->setAutoSize(true);
        }
        // Add more column widths here
        // : End
        
        // : Rename sheet
        // print(date('H:i:s') . " Rename sheet" . PHP_EOL);
        // $objPHPExcel->getActiveSheet()->setTitle(date('Y-m', strtotime('-1 month')));
        // : End
        
        // : Save spreadsheet to Excel 2007 file format
        print(date('H:i:s') . " Write to Excel2007 format" . PHP_EOL);
        print("</pre>" . PHP_EOL);
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($excelFile);
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        unset($objWriter);
        // : End
    }
    
    // : Magic
    /**
     * PullFandVContractData::__construct()
     * Class constructor
     */
    public function __construct()
    {
        try {
            
            $_config_path_env = getenv(self::ENV_VAR);
            
            if ($_config_path_env !== FALSE) {
                    
            } else {
                $this->printUsage(self::ERR_ENV_VAR_NOT_SET);
            }
            
            $_config_file = $_config_path_env . self::DS . self::CONFIG_FILE;
            
            if ($_config_file)
            
            $_config_data = $this->loadJSONFile($_config_file);
            
            if ($_config_data !== false) {
                print(PHP_EOL . "Dump of config_data array" . PHP_EOL);
                var_dump($_config_data);
                
                if (! array_key_exists('maxdb', $_config_data)) {
                    $this->printUsage(self::ERR_BWT_CONFIG_MAX_DB_NOT_SET);
                }
                
            } else {
                $this->printUsage();
            }
            
            $_options = getopt("m:");
            
            if (! array_key_exists("m", $_options)) {
                $this->printUsage();
            }
            
            $this->_mode = $_options["m"];
            
            // Set the filename for the XLSX file to be generated
            $_excelFileName = (string) date("Y-m-d") . "FandVContracts";
            
            $this->_maxdb_object = new PullDataFromMySQLQuery($_config_data);
            
            $this->setObjectRegIds();
            

            
            // Take data and write into an excel spreadsheet
            $this->writeExcelFile(dirname(__FILE__) . self::DS . $_excelFileName . ".xlsx", $consolidated);
            
        } catch (Exception $e) {
            $this->addErrorRecord("Caught exception: ", $e->getMessage(), "\n", __FUNCTION__, __CLASS__);
        }
        
        // If code reaches here then print any errors
        $this->printErrors();
        // : End
    }

    /**
     * PullFandVContractData::__destruct()
     * Class destructor
     * Allow for garbage collection
     */
    public function __destruct()
    {
        unset($this);
    }
    // : End
    
    // : Private Functions
    /**
     * PullFandVContractData::LoadJSONFile($_file)
     * Load config file containing json data
     *
     * @param return: $_result            
     */
    private function LoadJSONFile($_file)
    {
        // Default _result to FALSE
        $_result = false;
        
        try {
            
            if (file_exists($_file)) {
                $_json_file = file_get_contents($_file);
                
                if ($_json_file) {
                    $_json_data = json_decode($_json_file, true);
                    
                    if ($_json_data && is_array($_json_data)) {
                        $_result = $_json_data;
                    }
                }
            }
        } catch (Exception $e) {
            $this->addErrorRecord("Caught exception: ", $e->getMessage(), "\n", __FUNCTION__, __CLASS__);
            return false;
        }
        
        return $_result;
    }

    /**
     * PullFandVContractData::printUsage()
     * Prints the usage static property belonging to the class to output the usage of the script from the command line
     */
    private function printUsage($_msg = null)
    {
        // Clear the screen
        system('clear');

        // Print errors
        $this->printErrors();
        
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
     * PullFandVContractData::addErrorRecord($_errmsg, $_record, $_process)
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
    
    // : End
}

new PullFandVContractData();