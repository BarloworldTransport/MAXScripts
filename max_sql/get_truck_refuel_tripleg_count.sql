SET @tr=828;
SELECT t.id AS truck_id,
t.fleetnum,
t.active,
COUNT(r.id) AS refuel_count,
COUNT(tl.id) AS tripleg_count
FROM udo_truck AS t
LEFT JOIN udo_refuel AS r ON (r.truck_id=t.id)
LEFT JOIN udo_tripleg AS tl ON (tl.truck_id=t.id)
WHERE t.id=@tr;
