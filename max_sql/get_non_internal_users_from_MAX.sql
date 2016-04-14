SELECT p.email, CONCAT(p.first_name, ' ', p.last_name) AS fullnames, pu.status
FROM permissionuser AS pu
LEFT JOIN person AS p ON (p.id = pu.person_id)
WHERE pu.status like "enabled" AND (p.email NOT LIKE "%@manlinegroup.com" AND p.email NOT LIKE "%@manline.com" AND p.email NOT LIKE "%@bwtsgroup.com" AND p.email NOT LIKE "%@kaluma.com" AND p.email NOT LIKE "%@manline.co.za" AND p.email NOT LIKE "%@manline%" AND p.email NOT LIKE "%@bwtrans.co.za" AND p.email NOT LIKE "%@timber24.com" AND p.email NOT LIKE "%@bwlog.com")
ORDER BY p.first_name ASC;
