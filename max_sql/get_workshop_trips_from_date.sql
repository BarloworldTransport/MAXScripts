select ca.id, tl.loadingArrivalTime, tl.offloadingCompleted, t.id as truck_id, t.fleetnum from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) left join udo_truck as t on (t.id=tl.truck_id) where tl.workshopTrip=1 and tl.loadingArrivalTime >= "2014-06-15 00:00:00";

