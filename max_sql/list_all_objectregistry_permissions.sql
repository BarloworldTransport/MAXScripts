SELECT objr.id,
objr.handle as objectHandle,
powner.name AS objPrimaryOwner,
objr.primary_owner_crud as objPrimaryOwnerCRUD,
gowner.name AS objGroupOwner,
objr.group_owner_crud AS objGroupOwnerCRUD
FROM objectregistry AS objr
LEFT JOIN `group` AS gowner ON (gowner.id=objr.group_owner_group_id)
LEFT JOIN `group` AS powner ON (powner.id=objr.primary_owner_group_id)
ORDER BY objr.handle ASC;

