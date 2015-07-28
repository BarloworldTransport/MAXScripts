select tlc.id, tl.id, ca.id, db.id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) left join udo_debrief as db on (db.tripLeg_id=tl.id) where ca.id IN ();

