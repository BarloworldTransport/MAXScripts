SELECT obr.ID,
obr.name,
obr.handle,
obr.source,
pog.name AS 'Primary Owner',
obr.primary_owner_crud AS 'Primary Owner CRUD',
gog.name AS 'Group Owner',
obr.group_owner_crud AS 'Group Owner CRUD'
FROM objectregistry AS obr
LEFT JOIN `group` AS pog ON (pog.ID=obr.primary_owner_group_id)
LEFT JOIN `group` AS gog ON (gog.ID=obr.group_owner_group_id);