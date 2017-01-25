SELECT ca.tripNumber AS 'Trip Number',
t.fleetnum AS 'Truck',
f.name AS 'Primary Fleet for Truck'
FROM udo_cargo AS ca
LEFT JOIN udo_triplegcargo AS tlc ON (tlc.cargo_id = ca.id)
LEFT JOIN udo_tripleg AS tl ON (tl.id = tlc.tripLeg_id)
LEFT JOIN udo_truck AS t ON (t.id = tl.truck_id)
LEFT JOIN udo_fleet AS f ON (f.id = t.primaryFleet_id)
WHERE ca.tripNumber LIKE "NCP26495";
