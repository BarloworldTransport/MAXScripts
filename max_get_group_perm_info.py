#!/usr/bin/python
try:
	from mysql_library import mysql_object
except:
	print("An error occured when trying to import mysql_library.py")
	
test_query = 'SELECT id, first_name, last_name, email FROM person WHERE email LIKE "cwright%"'
max_db_conn = mysql_object('127.0.0.1', 'max2')
query_result = max_db_conn.mysql_query(test_query)
print(query_result)
