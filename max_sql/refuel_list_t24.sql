SELECT r.id, ron.orderNumber, d.staffNumber, t.fleetnum, r.fillDateTime, r.litres, r.odo, r.created_by
INTO OUTFILE '/tmp/t24_refuels3.csv'
FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
LINES TERMINATED BY '\n'
FROM udo_refuel AS r LEFT JOIN udo_refuelordernumber AS ron ON (ron.id=r.refuelOrderNumber_id) LEFT JOIN udo_driver AS d ON (d.id=r.driver_id) LEFT JOIN udo_truck AS t ON (t.id=r.truck_id) WHERE t.fleetnum LIKE "T%" AND DATE(r.time_created) >= DATE('2014-11-11 00:00:00') AND DATE(r.time_created) <= DATE('2014-11-14 00:00:00') AND r.created_by=1025;
