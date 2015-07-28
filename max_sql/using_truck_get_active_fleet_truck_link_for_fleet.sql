select ftl.id, t.fleetnum, drv.beginDate, drv.endDate, f.id as fleet_id, f.name as fleetname from udo_fleettrucklink as ftl
left join udo_truck as t on (t.id=ftl.truck_id) left join udo_fleet as f on (f.id=ftl.fleet_id) left join daterangevalue as drv on (drv.objectInstanceId=ftl.id)
where t.id IN (970) and
drv.type="FleetTruckLink" and
(drv.endDate IS NULL or
drv.endDate > DATE(CONCAT(CURDATE(), ' 00:00:00')));
