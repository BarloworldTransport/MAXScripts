SELECT ocap.handle AS ocapHandle,
objr.id AS objectRegistry_id,
objr.handle AS objectHandle,
powner.name AS ocaPrimaryOwner,
oca.primary_owner_crud as ocaPrimaryOwnerCRUD,
gowner.name AS ocaGroupOwner,
oca.group_owner_crud AS ocaGroupOwnerCRUD,
obpowner.name AS objPrimaryOwner,
objr.primary_owner_crud as objPrimaryOwnerCRUD,
obgowner.name AS objGroupOwner,
objr.group_owner_crud AS objGroupOwnerCRUD
FROM objectcrudactionprocess AS ocap
LEFT JOIN objectcrudaction AS oca ON (oca.id=ocap._ObjectCrudAction_id)
LEFT JOIN objectregistry AS objr ON (objr.id=oca.objectRegistry_id)
LEFT JOIN `group` AS gowner ON (gowner.id=oca.group_owner_group_id)
LEFT JOIN `group` AS powner ON (powner.id=oca.primary_owner_group_id)
LEFT JOIN `group` AS obgowner ON (obgowner.id=objr.group_owner_group_id)
LEFT JOIN `group` AS obpowner ON (obpowner.id=objr.primary_owner_group_id)
ORDER BY ocap.handle;

