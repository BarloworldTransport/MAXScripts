SET @email="disabled_heyImsomeuser@max.co.za";
SELECT CONCAT(p.first_name, " ", p.last_name) as contactName,
f.name as defaultFleet,
p.email,
pu.id as permissionUser_id,
p.id as person_id,
pu.status
FROM person as p
LEFT JOIN permissionuser AS pu ON (pu.person_id=p.id)
LEFT JOIN `preference` AS pr ON (pr._Group_id=pu.personal_group_id AND pr.handle = 'fleet_id')
LEFT JOIN udo_fleet AS f ON (f.id=pr.value)
WHERE p.email like @email;
SELECT g.name AS groupName
FROM person as p
LEFT JOIN permissionuser AS pu ON (pu.person_id=p.id)
LEFT JOIN group_role_link AS grl ON (grl.played_by_group_id=pu.personal_group_id)
LEFT JOIN `group` AS pg ON (pg.id=grl.played_by_group_id)
LEFT JOIN `group` AS g ON (g.id=grl.group_id)
WHERE p.email like @email
ORDER BY grl.id ASC;

# --=TEST EQUIVALENT==--
runq 'SELECT CONCAT(p.first_name, " ", p.last_name) as contactName,
f.name as defaultFleet,
p.email,
pu.id as permissionUser_id,
p.id as person_id
FROM person as p
LEFT JOIN permissionuser AS pu ON (pu.person_id=p.id)
LEFT JOIN `preference` AS pr ON (pr._Group_id=pu.personal_group_id AND pr.handle = "fleet_id")
LEFT JOIN udo_fleet AS f ON (f.id=pr.value)
WHERE p.email like "";'
runq 'SELECT g.name AS groupName
FROM person as p
LEFT JOIN permissionuser AS pu ON (pu.person_id=p.id)
LEFT JOIN group_role_link AS grl ON (grl.played_by_group_id=pu.personal_group_id)
LEFT JOIN `group` AS pg ON (pg.id=grl.played_by_group_id)
LEFT JOIN `group` AS g ON (g.id=grl.group_id)
WHERE p.email like ""
ORDER BY grl.id ASC;'
