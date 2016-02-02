#!/usr/bin/python

''' Object::max_get_group_perm_info.py
	@author Clinton Wright
	@author cwright@bwtrans.co.za
	@copyright 2016 onwards Manline Group (Pty) Ltd
	@license GNU GPL
	@see http://www.gnu.org/copyleft/gpl.html
'''

try:
	import os
except:
	print("An error occured when trying to import the os python library")

try:
	import pathlib
except:
	print("An error occured when trying to import the pathlib python library. Make sure that it is installed")
	
try:
	import json
except:
	print("An error occured when trying to import the json python library. Make sure that it is installed")

class file_methods(object):
	
	config_path = 'config'
	
	@staticmethod
	def load_json_file(aFile):
		
		full_path = os.path.join(file_methods.get_config_path(), aFile)
		
		p = pathlib.Path(full_path)
		if p.exists():
			
			try:
				main_config = json.loads(open(full_path).read())
				return main_config
			except:
				return False
		
	@staticmethod
	def get_config_path():
		try:
			return os.path.join(os.path.dirname(os.path.realpath(__file__)), file_methods.config_path)
		except:
			return False
