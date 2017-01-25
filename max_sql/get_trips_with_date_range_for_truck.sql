SET @startDate="2015-10-01 00:00:00";
SET @stopDate="2015-10-10 00:00:00";
SELECT ca.id AS cargo_id,
ca.tripNumber,
t.fleetnum as truck,
tl.loadingArrivalETA,
tl.offloadingArrivalETA,
tl.loadingArrivalTime,
tl.loadingStarted,
tl.offloadingCompleted,
tl.hollowTrip
FROM udo_triplegcargo AS tlc
LEFT JOIN udo_cargo AS ca ON (ca.id=tlc.cargo_id)
LEFT JOIN udo_tripleg AS tl ON (tl.id=tlc.tripLeg_id)
LEFT JOIN udo_truck AS t ON (t.id=tl.truck_id)
WHERE t.fleetnum like "263" AND ((tl.loadingStarted IS NOT NULL AND tl.loadingStarted >= @startDate AND tl.loadingStarted <= @stopDate) OR (tl.loadingStarted IS NULL AND tl.loadingArrivalETA >= @startDate AND tl.loadingArrivalETA <= @stopDate));

