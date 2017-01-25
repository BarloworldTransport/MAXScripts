# Set params
set @startDate = "2016-04-30 22:00:00";
set @stopDate = "2016-05-31 21:59:59";
set @truck = "135001";
set @bunit = "Dedicated";
SELECT ca.tripNumber,
tr.fleetnum as 'Truck Fleet Number',
trl.fleetNumber AS Trailer,
t.id AS 'Tripleg ID',
ca.companyInvoiceNumber AS 'Invoice Number',
bu.name AS 'Business Unit',
cu.tradingName AS 'Customer',
lf.name AS 'Location From',
lt.name AS 'Location To',
CONCAT(pd.first_name, ' ', pd.last_name) AS 'Full Name',
tl.loadingArrivalTime AS 'Loading Arrival Time',
tl.loadingStarted AS 'Loading Started',
tl.loadingFinished AS 'Loading Finished',
tl.timeLeft AS 'Time Left',
tl.offloadingArrivalTime AS 'Offloading Arrival Time',
tl.offloadingStarted AS 'Offloading Started',
tl.offloadingCompleted AS 'Offloading Completed',
tl.loadingArrivalETA AS 'Loading Arrival (ETA)',
tl.offloadingArrivalETA AS 'offloadingArrivalETA',
tl.kmsBegin AS 'Kms Begin',
tl.kmsEnd AS 'Kms End'
FROM udo_tripleg AS tl
LEFT JOIN udo_triplegcargo AS tlc ON (tlc.tripLeg_id = tl.id)
LEFT JOIN udo_cargo AS ca ON (ca.id = tlc.cargo_id)
LEFT JOIN udo_trip AS t ON (t.id = ca.trip_id)
LEFT JOIN udo_truck AS tr ON (tr.id = tl.truck_id)
LEFT JOIN udo_trailer AS trl ON (trl.id = tl.trailer_id)
LEFT JOIN udo_businessunit AS bu ON (bu.id = ca.businessUnit_id)
LEFT JOIN udo_location AS lf ON (lf.id = tl.locationFromPoint_id)
LEFT JOIN udo_location AS lt ON (lt.id = tl.locationToPoint_id)
LEFT JOIN udo_driver AS dr ON (dr.id = tl.driver_id)
LEFT JOIN person AS pd ON (pd.id = dr.person_id)
LEFT JOIN udo_customer AS cu ON (cu.id = ca.customer_id)
WHERE (tl.workshopTrip IS NULL OR tl.workshopTrip = "0") AND
((tl.loadingStarted IS NOT NULL AND tl.loadingStarted >= @startDate AND tl.loadingStarted <= @stopDate) OR
(tl.loadingStarted IS NULL AND tl.loadingArrivalETA >= @startDate AND tl.loadingArrivalETA <= @stopDate)) AND
tr.fleetnum = @truck AND
bu.name = @bunit
ORDER BY t.id ASC;
