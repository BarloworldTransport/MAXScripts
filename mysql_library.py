#!/usr/bin/python

''' Object::mysql_library.py
	@author Clinton Wright
	@author cwright@bwtrans.co.za
	@copyright 2016 onwards Manline Group (Pty) Ltd
	@license GNU GPL
	@see http://www.gnu.org/copyleft/gpl.html
'''

try:
	import mysql.connector
except ImportError:
	print("The python-mysql-connector-2.0 is required to run this program. Please install it and try again")

try:
	import bwtlib
except:
	print("The bwtlib.py module is required to run this script. Please verify that it exists")

try:
	import os
except:
	print("Failed to import config.py file. Please make certain that the file exists and has the expected data in it")

# Set config filename and build absolute path to the config file and store into a string
config_file = 'config.json'

# Load the JSON config file and store it into a dict variable
config_data = bwtlib.file_methods.load_json_file(config_file)


class mysql_object:
	
	cnx = None
	cursor = None
	
	def __init__(self):
		
		if type(config_data) is dict:
			db_config = config_data['mysql']
			self.mysql_connect(db_config)
		else:
			print("Failed to load JSON config file: " + os.path.join(bwtlib.file_methods.get_config_path, config_file))
			return None

	def mysql_connect(self, _config):
		
		try:
			self.cnx = mysql.connector.connect(**_config)
			return True
		except mysql.connector.Error as err:
			if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
				print("The supplied username and/or password is incorrect. Please verify and try again.")
			elif err.errno == errorcode.ER_BAD_DB_ERROR:
				print("Database does not exist")
			else:
				print(err)
		return False

	def mysql_close(self):
		
		try:
			self.cnx.close()
		except mysql.connect.Error as err:
			print("Failed to close database with error message\n ")
			print(err)
	
	def mysql_query(self, _query):
		
		try:
			
			self.cursor = self.cnx.cursor()
			self.cursor.execute(_query)
			self.cursor.fetchall()
			_count = self.cursor.rowcount
			_column_count = len(self.cursor.column_names)
			_data = [[0 for x in range(_column_count)] for x in range(_count)]

			self.cursor.execute(_query)

			for (idx, x) in enumerate(self.cursor):
				
				for(idy, y) in enumerate(self.cursor.column_names):
					_data[idx][idy] = x[idy]
				
			self.cursor.close()
			
			if len(_data) > 0:
				return _data
			else:
				return False

		except mysql.connector.Error as err:
			
			print("Failed to run a query:\n")
			print(err)
			self.cursor.close()

