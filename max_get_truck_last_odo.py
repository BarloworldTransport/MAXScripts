#!/usr/bin/python3

try:
	from mysql_library import MySQLObject
except:
	print("An error occured when trying to import mysql_library.py")

try:
	import re
except:
	print("Failed to import re python library. Please make sure that it is installed")

try:
	import sys
except ImportError:
	print("Failed to import sys python library module. Please check that it installed and try again")
	
try:
	import datetime
except:
	print("Failed to import datetime module")

''' Object::max_get_truck_last_odo.py
	@author Clinton Wright
	@author cwright@bwtrans.co.za
	@copyright 2016 onwards Manline Group (Pty) Ltd
	@license GNU GPL
	@see http://www.gnu.org/copyleft/gpl.html
'''

# Define variables

data = {}
csv_fields = []
capture_fields = 0

sql_query = "SELECT rf.id, \
rfo.orderNumber, \
tr.fleetnum, \
rf.odo, \
rf.litres, \
rf.fillDateTime, \
CONCAT(pc.first_name, \" \", pc.last_name) as createdBy, \
rf.time_created, \
CONCAT(plm.first_name, \" \", plm.last_name) as lastModifiedBy, \
rf.time_last_modified \
FROM udo_refuel AS rf \
LEFT JOIN udo_refuelordernumber AS rfo ON (rfo.id=rf.refuelOrderNumber_id) \
LEFT JOIN udo_truck AS tr ON (tr.id=rf.truck_id) \
LEFT JOIN permissionuser AS puc ON (puc.id=rf.created_by) \
LEFT JOIN person AS pc ON (pc.id=puc.person_id) \
LEFT JOIN permissionuser AS pulm ON (pulm.id=rf.last_modified_by) \
LEFT JOIN person AS plm ON (plm.id=pulm.person_id) \
WHERE tr.fleetnum = \"%s\" \
ORDER BY rf.fillDateTime DESC LIMIT 1;"

usageStr = "\nUsage: \
 max_get_truck_last_odo.py [truck1 [truck2] [truck3] [...]]\n\
 \n\
 try ./max_get_truck_last_odo.py 325410 D510\n\
 "

def _increment_hours_datetime(datetime_str, inc_hours):
	
	if type(datetime_str) is not str:
		datetime_str = str(datetime_str)
	
	# Convert date time value from DB to a datetime object to perform an operation on the datetime
	aDate = datetime.datetime.strptime(datetime_str, "%Y-%m-%d %H:%M:%S")
	
	# Add 2 hours to the datetime
	aDate = aDate + datetime.timedelta(hours=inc_hours)
	
	# Save the result to a string in the data dictionary
	date_result = aDate.strftime("%Y-%m-%d %H:%M:%S")
	
	if date_result and type(date_result) is str:
		return date_result
	else:
		return False
		
def is_number(value):
	
	try:
		float(value)
		return True
	except (ValueError):
		pass
		
	try:
		int(value)
		return True
	except (ValueError):
		return False
		
def write_csv_file(item_data, field_data):
	
	if type(item_data) is dict and len(item_data) > 0 and len(field_data) > 0 and type(field_data) is list:
		
		try:
			# Create new instance of datetime object and set datetime to current date time
			file_date = datetime.datetime.now()
			
			# Save string version of date
			file_date_str = file_date.strftime("%Y%m%d_%H%M%S")
			
			# Construct the filename of the file to be written
			file_name = "MAX-truck-last-odos-{}.csv".format(file_date_str)
			
			# Search and replace any whitespace characters in the filename
			p = re.compile('(\s)')
			file_name = p.sub('-', file_name)
			
			# Make all characters in the string lower case
			file_name = file_name.lower()
			
			# Convert list to comma seperated list
			fields_str = ",".join(field_data)
		
			# Open file for writing and overwrite if it already exists using w+ option
			f = open(file_name, 'w+')
			
			f.write('{}\n'.format(fields_str))
			
			for xid, x in enumerate(item_data):
				
				# Reset line_item variable for the next line
				line_item = ''
				
				if len(item_data[x]) > 0:
					
					for yid, y in enumerate(field_data):
						
						data_value = str(item_data[x][y])
						
						'''
						Build format string for field value
						If last value for the line then add new line character
						Else add comma after value
						'''
						
						if yid < len(field_data) - 1:
							end_str = ","
						else:
							end_str = "\n"
						
						'''
						Build format string for field value
						If alphanumeric and certain excluded fields, add double quotes around the value
						Else if numeric dont add quotes
						'''
						
						if is_number(data_value) and y != 'orderNumber' and y != 'fleetnum':
							fmt_str = "{}" + end_str
						else:
							fmt_str = "\"{}\"" + end_str
						
						# Save value into line string value
						line_item += fmt_str.format(item_data[x][y])

					if len(line_item) > 0 and line_item:
					
						# Write line
						f.write(line_item)
					
				else:
					
					# Write line
					f.write("," * int(len(field_data) - 1) + "\n")
					
			# Close the file handle
			f.close()
			print("\n\nSaved permissions to file: {}\n".format(file_name))
				
		except:
			
			# Try closing the file handle
			try:
				f.close()
			except:
				pass
				
			print("\n\nFailed writing permissions to file: {}\nError:\n\n{}\n".format(file_name, sys.exc_info()[0]))
			print(sys.exc_info())

if len(sys.argv) > 1:
	
	# Open a connection to MAX database
	max_db_conn = MySQLObject()

	for xid, x in enumerate(sys.argv):
		if xid != 0:
			if type(x) is not str:
				truck = str(x)
			else:
				truck = x
				
			if len(truck) > 0:
				data[x] = {}
				p = re.compile("(%s)")

				asql_query = p.sub(truck, sql_query)
				sql_result = max_db_conn.mysql_query(asql_query)
				
				if type(sql_result) is dict:
					
					if 'id' in sql_result[0]:
						
						for data_item in sql_result[0]:
							
							if capture_fields == 0:
								csv_fields.append(data_item)
								
							if data_item == 'fillDateTime' or data_item == 'time_created' or data_item == 'time_last_modified':
								
								# Add 2 hours to current datetime string value formatted as: %Y-%m-%d %H:%M:%S
								datetime_convert = _increment_hours_datetime(sql_result[0][data_item], 2)
								
								if datetime_convert != False:
									data[x][data_item] = datetime_convert

							else:
								data[x][data_item] = sql_result[0][data_item]
				else:
					print("ERROR: No results for truck: {}\n".format(truck))
				
				# test
				if len(csv_fields) > 0:
					capture_fields = 1

	if len(data) > 0 and type(data) is dict:

		csv_fields = sorted(csv_fields)
		write_csv_file(data, csv_fields)
		
	max_db_conn.mysql_close()
								
else:
	print(usageStr)
