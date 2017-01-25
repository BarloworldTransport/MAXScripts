SELECT ocap.id,
ocap.handle AS 'Process Handle',
objr.handle AS 'ObjectRegistry Handle',
powner.name AS 'Primary Owner',
oca.primary_owner_crud as 'Primary Owner CRUD',
gowner.name AS 'Group Owner',
oca.group_owner_crud AS 'Group Owner CRUD'
FROM objectcrudactionprocess AS ocap
LEFT JOIN objectcrudaction AS oca ON (oca.id=ocap._ObjectCrudAction_id)
LEFT JOIN objectregistry AS objr ON (objr.id=oca.objectRegistry_id)
LEFT JOIN `group` AS gowner ON (gowner.id=oca.group_owner_group_id)
LEFT JOIN `group` AS powner ON (powner.id=oca.primary_owner_group_id)
ORDER BY ocap.handle;