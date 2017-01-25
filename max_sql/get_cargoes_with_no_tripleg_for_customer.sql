SELECT ca.id as cargo_id, ca.tripNumber, ca.plannedLoadingArrivalDate, ca.plannedOffloadingArrivalDate, ca.time_created, CONCAT(p.first_name, ' ', p.last_name) as created_by
FROM udo_cargo AS ca
LEFT JOIN udo_customer AS cu ON (cu.id = ca.customer_id)
LEFT JOIN udo_triplegcargo AS tlc ON (tlc.cargo_id = ca.id)
LEFT JOIN permissionuser AS pu ON (pu.id = ca.created_by)
LEFT JOIN person AS p ON (p.id = pu.person_id)
WHERE tlc.id IS NULL AND cu.tradingName = "Glencore (Dedicated)";
