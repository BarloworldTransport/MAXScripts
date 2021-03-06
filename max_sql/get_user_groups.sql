SELECT g.name AS groupName
FROM person as p
LEFT JOIN permissionuser AS pu ON (pu.person_id=p.id)
LEFT JOIN group_role_link AS grl ON (grl.played_by_group_id=pu.personal_group_id)
LEFT JOIN `group` AS pg ON (pg.id=grl.played_by_group_id)
LEFT JOIN `group` AS g ON (g.id=grl.group_id)
WHERE p.email like "nokukhanya@manlinegroup.com"
ORDER BY grl.id ASC;
