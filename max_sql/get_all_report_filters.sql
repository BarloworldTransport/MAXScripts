SELECT r.name AS 'Report Name',
r.id AS 'Report ID',
dv.id AS 'DataView ID',
dv.filter AS 'Filter',
po.name AS 'Primary Owner',
r.primary_owner_crud AS 'Primary Owner CRUD',
go.name AS 'Group Owner',
r.group_owner_crud AS 'Group Owner CRUD'
FROM report AS r
LEFT JOIN dataview AS dv ON (dv.id = r.dataView_id)
LEFT JOIN `group` AS po ON (po.id = r.primary_owner_group_id)
LEFT JOIN `group` AS go ON (go.id = r.group_owner_group_id)
ORDER BY r.id DESC;