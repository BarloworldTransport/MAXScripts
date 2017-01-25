select c.id as cargo_id, t.id as tripleg_id from udo_tripleg as t left join udo_cargo as c on (c.trip_id=t.trip_id) where t.id IN ();
