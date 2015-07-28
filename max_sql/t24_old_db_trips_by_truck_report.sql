SELECT tr.fleetnum AS truckFleetNumber, ca.tripNumber, lf.name AS locationFrom, lt.name AS locationTo, CONCAT(p.first_name, " ", p.last_name) AS Driver, ADDTIME(tl.loadingArrivalTime, '0 2:0:0.0') as loadingArrivalTime, ADDTIME(tl.loadingStarted, '0 2:0:0.0') as loadingStarted, ADDTIME(tl.loadingFinished, '0 2:0:0.0') as loadingFinished, ADDTIME(tl.timeLeft, '0 2:0:0.0') as timeLeft, ADDTIME(tl.offloadingArrivalTime, '0 2:0:0.0') as offloadingArrivalTime, ADDTIME(tl.offloadingStarted, '0 2:0:0.0') as offloadingStarted, ADDTIME(tl.loadingArrivalETA, '0 2:0:0.0') as loadingArrivalETA, ADDTIME(tl.offloadingArrivalETA, '0 2:0:0.0') as offloadingArrivalETA, tl.kmsEnd,
(CASE
WHEN (tl.kmsEnd IS NOT NULL AND tl.kmsBegin IS NOT NULL AND tl.emptyKms IS NOT NULL) THEN ((tl.kmsBegin + tl.kmsEnd) - tl.emptyKms)
ELSE 0
END) as loadedKms,
tl.emptyKms,
(CASE
WHEN (tl.kmsEnd IS NOT NULL AND tl.kmsBegin IS NOT NULL) THEN (tl.kmsBegin + tl.kmsEnd)
ELSE 0
END) as invoiceKms,
ca.offloadingWeighGrossMass,
(CASE
WHEN (ca.offloadingWeighTareMass IS NOT NULL AND ca.offloadingWeighGrossMass IS NOT NULL) THEN (ca.offloadingWeighGrossMass - ca.offloadingWeighTareMass)
END) as nettMass,
tl.kmsBegin,
cu.tradingName,
(drv.value / 100) as rate,
ra.leadKms,
rt.name as rateType,
(CASE
WHEN (rt.id = 1 AND drv.value != 0) THEN drv.value
WHEN (rt.id = 2 and ra.minimumTons = 0 AND drv.value != 0) THEN ((drv.value / 100) * ca.tonsInvoiceable)
WHEN (rt.id = 3 AND ra.minimumTons = 1 AND ca.tonsInvoiceable < ra.minimumTons AND drv.value != 0) THEN ((drv.value / 100) * ra.minimumTons)
WHEN (rt.id = 4 AND ra.leadKms = 0 AND drv.value != 0) THEN ((drv.value / 100) * (tl.kmsEnd - tl.kmsBegin))
WHEN (rt.id = 4 AND ra.leadKms > 0) THEN ((drv.value / 100) * (ra.leadKms * 2))
WHEN (rt.id = 5 AND drv.value != 0) THEN ((drv.value / 100) * ca.pocketsInvoiceable)
WHEN (rt.id = 6 AND drv.value != 0) THEN ((drv.value / 100) * ca.litresInvoiceable)
WHEN (rt.id = 7 AND drv.value != 0) THEN ((drv.value / 100) * ca.palletsInvoiceable)
ELSE 0
END) AS Income,
ca.tonsActual
FROM udo_triplegcargo AS tlc
INNER JOIN udo_cargo AS ca on (ca.id=tlc.cargo_id)
INNER JOIN udo_tripleg AS tl on (tl.id=tlc.tripLeg_id)
INNER JOIN udo_rates AS ra on (ra.id=ca.rate_id)
INNER JOIN udo_ratetype AS rt on (rt.id=ra.ratetype_id)
INNER JOIN udo_location AS lf on (ca.locationFrom_id)
INNER JOIN udo_location AS lt on (ca.locationTo_id)
INNER JOIN udo_truck AS tr on (tr.id=tl.truck_id)
INNER JOIN daterangevalue AS drv on (drv.objectInstanceId = ca.rate_id and drv.type="Rate")
INNER JOIN udo_driver AS dr on (dr.id=tl.driver_id)
INNER JOIN person AS p on (p.id=dr.person_id)
INNER JOIN udo_customer AS cu ON (cu.id=ca.customer_id)
WHERE (tl.workshopTrip = 0 AND tl.hollowTrip = 0) AND ((tl.offloadingArrivalTime IS NOT NULL AND tl.offloadingArrivalTime >= "2013-02-28 22:00:00" AND tl.offloadingArrivalTime <= "2014-09-30 22:00:00") OR (tl.offloadingArrivalTime IS NULL AND tl.offloadingArrivalETA >= "2013-02-28 22:00:00" AND tl.offloadingArrivalETA <= "2014-09-30 22:00:00"));
