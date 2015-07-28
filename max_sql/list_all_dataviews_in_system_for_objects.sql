SELECT dv.id AS dataView_id,
dv.name AS dataViewName,
powner.name AS PrimaryOwner,
dv.primary_owner_crud AS PrimaryOwnerCRUD,
gowner.name AS GroupOwner,
dv.group_owner_crud
FROM dataview AS dv
LEFT JOIN `group` AS powner ON (powner.id=dv.primary_owner_group_id)
LEFT JOIN `group` AS gowner ON (gowner.id=dv.group_owner_group_id)
LIMIT 10\G
