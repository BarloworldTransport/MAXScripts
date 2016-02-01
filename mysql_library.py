#!/usr/bin/python
try:
	import mysql.connector
except ImportError:
	print("The python-mysql-connector-2.0 is required to run this program. Please install it and try again")


class mysql_object:

	config = {
		"user" : "root",
		"password" : "kaluma",
		"host" : "127.0.0.1",
		"database" : "max2"
	}
	
	cnx = None
	cursor = None
	
	def __init__(self, _host, _db):

		if(type(_host) is str and type(_db) is str):

			self.config['host'] = _host
			self.config['database'] = _db
			
			self.mysql_connect(self.config)

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
