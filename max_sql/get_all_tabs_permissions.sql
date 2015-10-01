SELECT t.ID,
t.Type,
t.name AS 'Tab Name',
t.Pretty_Name AS 'Pretty Tab Name',
t.hidden,
t.show_menu,
pt.Pretty_Name AS 'Parent Tab',
pog.name AS 'Primary Owner',
t.primary_owner_crud AS 'Primary Owner CRUD',
gog.name AS 'Group Owner',
t.group_owner_crud AS 'Group Owner CRUD'
FROM `tab` AS t
LEFT JOIN `tab` AS pt ON (pt.ID=t.parent_tab_id)
LEFT JOIN `group` AS pog ON (pog.ID=t.primary_owner_group_id)
LEFT JOIN `group` AS gog ON (gog.ID=t.group_owner_group_id)
