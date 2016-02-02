#!/usr/bin/python

''' Object::max_get_group_perm_info.py
	@author Clinton Wright
	@author cwright@bwtrans.co.za
	@copyright 2016 onwards Manline Group (Pty) Ltd
	@license GNU GPL
	@see http://www.gnu.org/copyleft/gpl.html
'''

try:
	from mysql_library import mysql_object
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
WHERE (`powner`.`id` = %pgid) OR (`gowner`.`id` = %gid);",
		
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
		
	"max_get_objreg_permissions" : "SELECT `objr`.`ID`,\
`objr`.`handle`,\
`powner`.`name` AS `primaryOwner_name`,\
`objr`.`primary_owner_crud` as `primaryOwner_crud`,\
`gowner`.`name` AS `groupOwner_name`,\
`objr`.`group_owner_crud` AS `groupOwner_crud`\
FROM `objectregistry` AS `objr`\
LEFT JOIN `group` AS `gowner` ON (`gowner`.`ID`=`objr`.`group_owner_group_id`)\
LEFT JOIN `group` AS `powner` ON (`powner`.`ID`=`objr`.`primary_owner_group_id`)\
WHERE `objr`.`primary_owner_group_id` = %gid OR `objr`.`group_owner_group_id` = %gid\
ORDER BY `objr`.`handle` ASC;"
}

usageStr = "\nUsage: \
 max_get_group_perm_info.py [group]\n\
 \n\
 try ./max_get_group_perm_info.py 'Fleet Controller'\n\
 "

def getGroupId(group_name):
	
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
		
def getSubGroupsForGroups(group_id):
	
	if type(group_id) is int:
		group_id = str(group_id)
	
	# Setup variables
	group_id = str(group_id)
	
	# Create empty dict
	subgroup_data = {}
	
	# Prepare query string by replacing placeholders with values
	p = re.compile('(%gid)')
	query_string = p.sub(group_id, sql_queries['max_get_subgroups_for_group'])
	query_result = max_db_conn.mysql_query(query_string)
						
	if type(query_result) is dict and len(query_result) > 0:
		return query_result
	else:
		print("No data returned")
		return False

if len(sys.argv) > 1:
	
	# Store argument intended to be the group to check permissions against on MAX
	arg_group = sys.argv[1]

	if type(arg_group) is str:
		
		# Open a connection to MAX database
		max_db_conn = mysql_object()
			
		# Get the group ID from MAX DB
		group_id = getGroupId(arg_group)
		if group_id:
			
			# Get the group sub groups
			parent_sub_groups = getSubGroupsForGroups(group_id)
			
			if parent_sub_groups:
				
				for parent_sub_group in parent_sub_groups:
					
					# Get current index subgroup been iterated
					curr_sub_group = parent_sub_groups[parent_sub_group]['groupName']
					
					# Fetch subgroup group id
					curr_group_id = getGroupId(curr_sub_group)
					
					if curr_group_id:
						
						# Fetch all children groups of subgroup
						curr_sub_group_groups = getSubGroupsForGroups(curr_group_id)
						
						if curr_sub_group_groups:
							
							# Instantiate subgroups index for subgroup index under parent dict
							parent_sub_groups[parent_sub_group]['subgroups'] = {}
							
							for curr_sub_group_group in curr_sub_group_groups:
								
								# Add subgroups to parent group -> sub group -> subgroup index of dict
								parent_sub_groups[parent_sub_group]['subgroups'][curr_sub_group_group] = curr_sub_group_groups[curr_sub_group_group]['groupName']
								
else:
	print(usageStr)
	
# NOTE: The above only searches 1 level below parent subgroup for subgroups. Need function recursion to exhaust all subgroups below parent group
