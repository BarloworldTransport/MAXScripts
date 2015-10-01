select ftl.truck_id, t.fleetnum, ftl.fleet_id, f.name as fleetname, drv.beginDate, drv.endDate
from udo_fleettrucklink as ftl
left join udo_truck as t on (t.id=ftl.truck_id)
left join udo_fleet as f on (f.id=ftl.fleet_id)
left join daterangevalue as drv on (drv.objectInstanceId=ftl.id)
where (drv.beginDate IS NOT NULL) AND (drv.endDate IS NULL OR drv.endDate >= DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')) AND t.fleetnum like "144006";
