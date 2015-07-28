SET @tr1=9;
SET @tr2=99;

SELECT truck_id,
count(id) as refuel_count
FROM udo_refuel
WHERE truck_id=@tr1;

SELECT truck_id,
count(id) as tripleg_count
FROM udo_tripleg
WHERE truck_id=@tr1;

SELECT truck_id,
count(id) as fandv_count
FROM udo_fandvcontracttruck_link
WHERE truck_id=@tr1;

select ftl.id, t.fleetnum, drv.beginDate, drv.endDate, f.id as fleet_id, f.name as fleetname from udo_fleettrucklink as ftl
left join udo_truck as t on (t.id=ftl.truck_id) left join udo_fleet as f on (f.id=ftl.fleet_id) left join daterangevalue as drv on (drv.objectInstanceId=ftl.id)
where t.id IN (@tr1) and
drv.type="FleetTruckLink" and
(drv.endDate IS NULL or
drv.endDate > DATE(CONCAT(CURDATE(), ' 00:00:00')));

SELECT truck_id,
count(id) as refuel_count
FROM udo_refuel
WHERE truck_id=@tr2;

SELECT truck_id,
count(id) as tripleg_count
FROM udo_tripleg
WHERE truck_id=@tr2;

SELECT truck_id,
count(id) as fandv_count
FROM udo_fandvcontracttruck_link
WHERE truck_id=@tr2;

select ftl.id, t.fleetnum, drv.beginDate, drv.endDate, f.id as fleet_id, f.name as fleetname from udo_fleettrucklink as ftl
left join udo_truck as t on (t.id=ftl.truck_id) left join udo_fleet as f on (f.id=ftl.fleet_id) left join daterangevalue as drv on (drv.objectInstanceId=ftl.id)
where t.id IN (@tr2) and
drv.type="FleetTruckLink" and
(drv.endDate IS NULL or
drv.endDate > DATE(CONCAT(CURDATE(), ' 00:00:00')));
