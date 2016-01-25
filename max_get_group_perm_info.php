<?php
// Error reporting
error_reporting(E_ALL);

//: Includes
/** MySQL query pull and return data class */
include dirname(__FILE__) . '/PullDataFromMySQLQuery.php';
//: End

/*
 * Script needs to fetch all of the following associated with the specified user group on MAX:
 * 
 * Process
 * DataView (ItemListDataView)
 * ObjectRegistry Perm (Ownership and CRUD Perms)
 * ObjectRegistry Perm Templates
 * All Sub Groups
 * 
 * Break the above info into sections in a listing as a return result at the end of the script run
 */

/** Object::max_get_group_perm_info
 * @author Clinton Wright
 * @author cwright@bwtsgroup.com
 * @copyright 2011 onwards Manline Group (Pty) Ltd
 * @license GNU GPL
 * @see http://www.gnu.org/copyleft/gpl.html
 */
class max_get_group_perm_info {
    CONST TENANT_DB = "max2";
    CONST HOST_DB = "192.168.1.19";
    CONST DEFAULT_LIMIT = 5;
    CONST SQL_QUERY = array(
		"max_get_processes_for_group" => "SELECT ocap.id,
ocap.handle AS 'processHandle',
objr.handle AS 'objRegHandle',
powner.name AS 'primaryOwner',
oca.primary_owner_crud as 'primaryOwnerCRUD',
gowner.name AS 'groupOwner',
oca.group_owner_crud AS 'groupOwnerCRUD'
FROM objectcrudactionprocess AS ocap
LEFT JOIN objectcrudaction AS oca ON (oca.id=ocap._ObjectCrudAction_id)
LEFT JOIN objectregistry AS objr ON (objr.id=oca.objectRegistry_id)
LEFT JOIN `group` AS gowner ON (gowner.id=oca.group_owner_group_id)
LEFT JOIN `group` AS powner ON (powner.id=oca.primary_owner_group_id)
WHERE (powner.id = %poid) OR (gowner.id = %goid)
ORDER BY ocap.handle;",
		"max_get_subgroups_for_group" => "b"
	);
    	//: Variables
    	private static $_usage = array(
        	"max_get_group_perm_info - Fetch processes, views, object and object permission templates for a MAX user group",
	        "",
	        "Usage: max_get_group_perm_info.php -g 'GROUPNAME'",
	        "",
	        "Arguments:",
	        "",
	        "Required options:",
	        "",
	        "Optional options:",
	        "",
        	"Example:",
	        "",
			""
    	);

	//: Public functions
	//: Accessors

	//: Magic
	/** runsqlfile::__construct()
	* Class constructor
	*/
	public function __construct() {
	// Construct an array with predefined date(s) which we will use to run a report
		var_dump(SELF::SQL_QUERY);
		//$options = getopt("t:l:");
		/*
		$_result_limit = intval($options["l"]) ? intval($options["l"]) : SELF::DEFAULT_LIMIT;
		
        $sqlfile = $options["t"];
        if ($sqlfile) {
            $_ids = explode(",", $sqlfile);
        } else {
		$this->printUsage();
        }
        
        

        
        $sqlData = new PullDataFromMySQLQuery(self::TENANT_DB, self::HOST_DB);
        // Run query and return result
        if (is_array($_ids)) {
            $_x = 1;
            foreach($_ids as $_id) {
                
                $_query = preg_replace("/%s/", $_id, self::SQL_QUERY);
                $_query = preg_replace("/%d/", $_result_limit, $_query);
                
                $_data = $sqlData->getDataFromQuery($_query);

        		if ($_data) {
					
					// Clear the screen
					system('clear');
					system('clear');
					
                    foreach($_data as $_key => $_value) {
                        echo $_x . PHP_EOL;
                        if (is_array($_value)) {
                            foreach($_value as $_key2 => $_value2) {
                                echo "$_key2: $_value2" . PHP_EOL;
                            }
                        }
                        $_x++;
                    }
                } else {
                    echo "NO RESULT" . PHP_EOL;
                }
            }
        }
        */
	}

	/** runsqlfile::__destruct()
		* Class destructor
		* Allow for garbage collection
		*/
	public function __destruct() {
		unset($this);
	}
	//: End
    // : Private Functions
    /**
     * get_refuel_id::printUsage($_msg = null)
     * Prints the usage static property belonging to the class to output the usage of the script from the command line
     */
    private function printUsage($_msg = null)
    {
        // Clear the screen
        system('clear');
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

} new max_get_group_perm_info();
