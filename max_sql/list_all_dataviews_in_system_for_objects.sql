SELECT dv.id AS id,
dv._type AS `type`,
dv.name AS dataViewName,
objr.handle AS objectHandle,
objr.name AS objectName,
dv.filter,
powner.name AS primaryOwner,
dv.primary_owner_crud AS primaryOwnerCRUD,
gowner.name AS groupOwner,
dv.group_owner_crud as groupOwnerCRUD
FROM dataview AS dv
LEFT JOIN `group` AS powner ON (powner.id=dv.primary_owner_group_id)
LEFT JOIN `group` AS gowner ON (gowner.id=dv.group_owner_group_id)
LEFT JOIN objectregistry AS objr ON (objr.id=dv.objectRegistry_id);
