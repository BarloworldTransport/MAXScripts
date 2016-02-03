#!/usr/bin/python

''' Object::max_get_group_perm_info.py
	@author Clinton Wright
	@author cwright@bwtrans.co.za
	@copyright 2016 onwards Manline Group (Pty) Ltd
	@license GNU GPL
	@see http://www.gnu.org/copyleft/gpl.html
'''

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
	
sql_queries = {
	"max_get_group_id" : "SELECT `id` \
FROM `group` \
WHERE `name` = '%s'",
	"max_get_processes_for_group" : "SELECT ocap.id, \
ocap.handle AS 'processHandle', \
objr.handle AS 'objRegHandle', \
powner.name AS 'primaryOwner', \
oca.primary_owner_crud as 'primaryOwnerCRUD', \
gowner.name AS 'groupOwner', \
oca.group_owner_crud AS 'groupOwnerCRUD' \
FROM objectcrudactionprocess AS ocap \
LEFT JOIN objectcrudaction AS oca ON (oca.id=ocap._ObjectCrudAction_id) \
LEFT JOIN objectregistry AS objr ON (objr.id=oca.objectRegistry_id) \
LEFT JOIN `group` AS gowner ON (gowner.id=oca.group_owner_group_id) \
LEFT JOIN `group` AS powner ON (powner.id=oca.primary_owner_group_id) \
WHERE (powner.id = %gid) OR (gowner.id = %gid) \
ORDER BY ocap.handle;",

	"max_get_subgroups_for_group" : "SELECT `rg`.`name` AS `groupName` \
FROM `group_role_link` AS `grl` \
LEFT JOIN `group` AS `pg` ON (`pg`.id = `grl`.`played_by_group_id`) \
LEFT JOIN `group` AS `rg` ON (`rg`.`id` = `grl`.`group_id`) \
WHERE `pg`.`id` = %gid \
ORDER BY `rg`.`name`;",

	"max_get_dataviews" : "SELECT `dv`.`id`, \
`dv`.`_type` AS `type`, \
`dv`.`name` AS `dataViewName`, \
`objr`.`id` AS `objReg_id`, \
`objr`.`handle` AS 'objRegHandle', \
`objr`.`name` AS 'objRegName', \
`dv`.`filter` AS 'dataViewFilter', \
`powner`.`name` AS 'primaryOwner', \
`dv`.`primary_owner_crud` AS 'primaryOwnerCRUD', \
`gowner`.`name` AS 'groupOwner', \
`dv`.`group_owner_crud` AS 'groupOwnerCRUD' \
FROM `dataview` AS `dv` \
LEFT JOIN `group` AS `powner` ON (`powner`.`id`=`dv`.`primary_owner_group_id`) \
LEFT JOIN `group` AS `gowner` ON (`gowner`.`id`=`dv`.`group_owner_group_id`) \
LEFT JOIN `objectregistry` AS `objr` ON (`objr`.`id`=`dv`.`objectRegistry_id`) \
WHERE (`powner`.`id` = %gid) OR (`gowner`.`id` = %gid);",
		
	"max_get_permission_templates" : "SELECT `gpt`.`ID` AS `permissionTemplate_id`,\
`gpt`.`condition`,\
`obr`.`name` AS `objectRegistry_name`,\
`gr`.`name` AS `defaultGroup_name`,\
`tpog`.`name` AS `templatePrimaryOwner_name`,\
`gpt`.`template_primary_owner_crud` AS `templatePrimaryOwner_crud`,\
`tgog`.`name` AS `templateGroupOwner_name`,\
`gpt`.`template_group_owner_crud` AS `templateGroupOwner_crud`,\
`gpt`.`rule`\
FROM `grouppermissiontemplate` AS `gpt`\
LEFT JOIN `objectregistry` AS `obr` ON (`obr`.`ID`=`gpt`.`_ObjectRegistry_id`)\
LEFT JOIN `group` AS `gr` ON (`gr`.`ID`=`gpt`.`group_id`)\
LEFT JOIN `group` AS `tpog` ON (`tpog`.`ID`=`gpt`.`template_primary_owner_group_id`)\
LEFT JOIN `group` AS `tgog` ON (`tgog`.`ID`=`gpt`.`template_group_owner_group_id`)\
WHERE `obj`.`ID` IN (%objid);",
		
	"max_get_objreg_permissions" : "SELECT `objr`.`ID`, \
`objr`.`handle`, \
`powner`.`name` AS `primaryOwner_name`, \
`objr`.`primary_owner_crud` as `primaryOwner_crud`, \
`gowner`.`name` AS `groupOwner_name`, \
`objr`.`group_owner_crud` AS `groupOwner_crud` \
FROM `objectregistry` AS `objr` \
LEFT JOIN `group` AS `gowner` ON (`gowner`.`ID`=`objr`.`group_owner_group_id`) \
LEFT JOIN `group` AS `powner` ON (`powner`.`ID`=`objr`.`primary_owner_group_id`) \
WHERE `objr`.`primary_owner_group_id` = %gid OR `objr`.`group_owner_group_id` = %gid \
ORDER BY `objr`.`handle` ASC;"
}

usageStr = "\nUsage: \
 max_get_group_perm_info.py [group]\n\
 \n\
 try ./max_get_group_perm_info.py 'Fleet Controller'\n\
 "

def get_group_id(group_name):
	
	if type(group_name) is str:
		
		# Prepare query string by replacing placeholders with values
		p = re.compile('(%s)')
		query_string = p.sub(group_name, sql_queries['max_get_group_id'])
		query_result = max_db_conn.mysql_query(query_string)
		
	if type(query_result) is dict and len(query_result) > 0:
		
		# Check if key 'id' exists in the returned result
		if 'id' in query_result[0]:
			return query_result[0]['id']
		else:
			print("Failed to get group ID for group: " + group_name)
		
	else:
		print("No data returned")
		return False
		
def run_query(group_id, search_str, query_str):
	
	try:
		
		if type(group_id) is int:
			group_id = str(group_id)
			
		if type(search_str) is str and type(query_str) is str:
		
			# Prepare query string by replacing placeholders with values
			p = re.compile(search_str)
			query_string = p.sub(group_id, query_str)
			query_result = max_db_conn.mysql_query(query_string)
		
		else:
			print("search pattern and query must be string")
			return False
						
		if type(query_result) is dict and len(query_result) > 0:
			return query_result
		else:
			return False
	except:
		print("An error occured while processing the query")
		return False
		
def fetch_all_children_for_parent_group(group_name):
	
	# Get group_id for parent group
	group_id = get_group_id(group_name)
		
	if group_id:

		# Fetch all first level children groups for the parent
		sub_groups_data = run_query(group_id, "(%gid)", sql_queries['max_get_subgroups_for_group'])

		if sub_groups_data:
				
			# Iterate through the found 1st level children groups of the parent
			for sub_group in sub_groups_data:
					
				# Get current index subgroup been iterated
				curr_sub_group = sub_groups_data[sub_group]['groupName']
					
				# Fetch subgroup group id
				curr_sub_group_id = get_group_id(curr_sub_group)
					
				# Fetch subgroup subgroups
				curr_sub_group_groups = run_query(curr_sub_group_id, '(%gid)', sql_queries['max_get_subgroups_for_group'])
					
				if curr_sub_group_groups:
						
					'''
					Recursively call this function again to find and build all this subgroups subgroups and
					return value as dict
					'''
					sub_groups_data[sub_group]['subgroups'] = fetch_all_children_for_parent_group(curr_sub_group)
			
			return sub_groups_data
	else:
		# Return empty dict object if subgroups found for subgroup
		return {}

def build_list_groups_from_dict(groups, group_list):
	
	# Iterate dict until we get to the group names captured within
	if groups and type(groups) is dict:
		
		for group_item in groups:
			
			for group_item_data in groups[group_item]:
				
				if type(groups[group_item][group_item_data]) is str:
				
					# If the index key is str then it is an item and add it to the list	
					group_list.append(groups[group_item][group_item_data])
					
				elif type(groups[group_item][group_item_data]) is dict:
					
					for sub_group_item in groups[group_item][group_item_data]:
						
						if 'groupName' in groups[group_item][group_item_data][sub_group_item]:

							group_list.append(groups[group_item][group_item_data][sub_group_item]['groupName'])
						
						if 'subgroups' in groups[group_item][group_item_data][sub_group_item]:
							
							# Recursively run subgroups index od dict through this function again to add all its sub groups
							build_list_groups_from_dict(groups[group_item][group_item_data][sub_group_item]['subgroups'], group_list)
							
def print_permission_data(perm_data):
	
	if type(perm_data) is dict and len(perm_data) > 0:
		
		try:
		
			file_name = "MAX-permission-info-{}.txt".format(arg_group)
			
			p = re.compile('(\s)')
			file_name = p.sub('-', file_name)
			
			file_name = file_name.lower()
		
			f = open(file_name, 'w+')
		
			f.write('MAX permission listing for group: {}'.format(arg_group))
			
			f.write('\n\nSub Groups Listing:\n')
			
			for data_item in perm_data:
				f.write("{}\n".format(data_item))
		
			value_str = ''
		
			for data_item in perm_data:
			
				f.write('\n\nSub Group: {}\n'.format(data_item))
			
				for data_item_key in perm_data[data_item]:
				
					if type(data_item_key) is str and perm_data[data_item][data_item_key] != False:

						if data_item_key == 'obj_reg_perms' or data_item_key == 'dataview_perms' or data_item_key == 'process_perms':
						
							f.write('\n\n{}:\n'.format(data_item_key))
						
							for sub_data in perm_data[data_item][data_item_key]:
							
								f.write("\nID: {}\n".format(sub_data))
								
								for sub_data_item in perm_data[data_item][data_item_key][sub_data]:
								
									if type(perm_data[data_item][data_item_key][sub_data][sub_data_item]) is str:
									
										# assign str to the value
										value_str = perm_data[data_item][data_item_key][sub_data][sub_data_item]
								
									if type(perm_data[data_item][data_item_key][sub_data][sub_data_item]) is set:
									
										# Convert set to a string
										value_str = ', '.join(perm_data[data_item][data_item_key][sub_data][sub_data_item])
								
									f.write("{}: {}\n".format(sub_data_item, value_str))
			f.close()
			print("\n\nSaved permissions to file: {}\n".format(file_name))
				
		except:
			print("\n\nFailed writing permissions to file: {}\nError:\n\n{}\n".format(file_name, sys.exc_info()[0]))
			print(sys.exc_info())
			
if len(sys.argv) > 1:
	
	# Store argument intended to be the group to check permissions against on MAX
	arg_group = sys.argv[1]

	if type(arg_group) is str:
		
		# Open a connection to MAX database
		max_db_conn = MySQLObject()
			
		'''
		Recursively look for each subgroups subgroups until all children groups are
		found and a complete group tree is built identifying every group beneath the parent group
		'''
		sub_group_tree = fetch_all_children_for_parent_group(arg_group)

		'''
		Search through all found groups and remove duplicate groups and build into an list
		with which to search through to find all related dataviews, objectregistry and process
		ownership and permissions
		'''
		found_groups_list = []
		build_list_groups_from_dict(sub_group_tree, found_groups_list)
		group_data = {}
		
		for group_item in found_groups_list:
			
			group_data[group_item] = {}
			group_data[group_item]['obj_reg_perms'] = {}
			group_data[group_item]['dataview_perms'] = {}
			group_data[group_item]['process_perms'] = {}
			
			curr_group_id = get_group_id(group_item)
			
			if curr_group_id:

				obj_reg_perms = run_query(curr_group_id, '(%gid)', sql_queries['max_get_objreg_permissions'])
				
				dataview_perms = run_query(curr_group_id, '(%gid)', sql_queries['max_get_dataviews'])
				
				process_perms = run_query(curr_group_id, '(%gid)', sql_queries['max_get_processes_for_group'])
				
				group_data[group_item]['obj_reg_perms'] = obj_reg_perms
				
				group_data[group_item]['dataview_perms'] = dataview_perms
				
				group_data[group_item]['process_perms'] = process_perms

		if len(group_data) > 0:
			
			print_permission_data(group_data)
		

		max_db_conn.mysql_close()
								
else:
	print(usageStr)
