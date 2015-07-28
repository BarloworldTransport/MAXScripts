select ca.id as ca_id, tl.id as tl_id, tlc.id as tlc_id, tl.loadingStarted, tl.offloadingCompleted, tl.kmsBegin, tl.kmsEnd, t.id as truck_id, ca.rate_id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) left join udo_truck as t on (t.id=tl.truck_id) left join udo_fleet as f on (f.id=t.primaryFleet_id) where ca.id in ();
