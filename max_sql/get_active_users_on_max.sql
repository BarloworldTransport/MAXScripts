SELECT CONCAT(p.first_name, ' ', p.last_name) AS fullNames,
p.email,
pu.Last_Access,
p.jobTitle,
p.company,
IF(p.address_line_1 IS NOT NULL, p.address_line_1, NULL) AS address
FROM permissionuser AS pu
LEFT JOIN person AS p ON (p.id=pu.person_id)
WHERE pu.status = 1
ORDER BY p.email ASC;