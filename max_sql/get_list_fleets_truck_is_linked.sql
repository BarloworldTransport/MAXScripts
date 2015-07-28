select f.name as fleetname, t.fleetnum, t.id as truck_id from udo_fleettrucklink as ftl left join udo_fleet as f on (f.id=ftl.fleet_id) left join udo_truck as t on (t.id=ftl.truck_id) where ftl.truck_id IN ();

