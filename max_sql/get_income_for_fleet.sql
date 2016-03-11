select FORMAT(SUM(`income`), 2) as budgetTotal from udo_truckbudget
where `date` >= DATE_FORMAT(NOW(), "%Y-%m-01") and `date` <= DATE_FORMAT(NOW(), "%Y-%m-%d")
and `truck_id` IN (select ftl.truck_id
from udo_fleettrucklink as ftl
left join udo_fleet as f on (f.id = ftl.fleet_id)
left join daterangevalue as drv on (drv.objectInstanceId=ftl.id)
where (drv.beginDate IS NOT NULL) AND (drv.endDate IS NULL OR drv.endDate >= DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')) AND f.name = "Freight SA (Own)");
