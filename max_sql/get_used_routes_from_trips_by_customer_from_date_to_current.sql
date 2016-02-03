SELECT CONCAT(lf.name, " TO ", lt.name)
FROM udo_tripleg AS tl
LEFT JOIN udo_triplegcargo AS tlc ON (tlc.tripLeg_id = tl.id)
LEFT JOIN udo_cargo AS ca ON (ca.id = tlc.cargo_id)
LEFT JOIN udo_location AS pf ON (pf.id = tl.locationFromPoint_id)
LEFT JOIN udo_location AS pt ON (pt.id = tl.locationToPoint_id)
LEFT JOIN udo_route AS ro ON (ro.locationFrom_id = pf.parent_id AND ro.locationTo_id = pt.parent_id)
LEFT JOIN udo_location AS lf ON (lf.id = ro.locationFrom_id)
LEFT JOIN udo_location AS lt ON (lt.id = ro.locationTo_id)
WHERE ca.customer_id = 28803
AND ((tl.loadingStarted IS NULL AND tl.loadingArrivalTime >= "2014-12-31 22:00:00") OR (tl.loadingStarted IS NOT NULL AND tl.loadingStarted >= "2014-12-31 22:00:00"))
GROUP BY ro.id
ORDER BY lf.name ASC;
