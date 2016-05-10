SELECT ca.id AS cargo_id
FROM udo_triplegcargo AS tlc
LEFT JOIN udo_cargo AS ca ON (ca.id = tlc.cargo_id)
LEFT JOIN udo_tripleg AS tl ON (tl.id = tlc.tripLeg_id)
WHERE tl.workshopTrip IS NULL AND ca.businessUnit_id = 7 ORDER BY tlc.id DESC LIMIT 1;
