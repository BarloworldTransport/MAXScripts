SELECT dv.id,
dv._type AS `type`,
dv.name AS 'Name',
objr.handle AS 'ObjectRegistry Handle',
objr.name AS 'ObjectRegistry Name',
dv.filter AS 'Filter',
powner.name AS 'Primary Owner',
dv.primary_owner_crud AS 'Primary Owner CRUD',
gowner.name AS 'Group Owner',
dv.group_owner_crud AS 'Group Owner CRUD'
FROM dataview AS dv
LEFT JOIN `group` AS powner ON (powner.id=dv.primary_owner_group_id)
LEFT JOIN `group` AS gowner ON (gowner.id=dv.group_owner_group_id)
LEFT JOIN objectregistry AS objr ON (objr.id=dv.objectRegistry_id);
