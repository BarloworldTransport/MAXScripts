select ca.id as cargo_id, ca.tripNumber, tl.id as tripLeg_id, ca.imageGroup_id as ocr_id, df.id as debrief_id from udo_triplegcargo as tlc
left join udo_cargo as ca on (ca.id=tlc.cargo_id) left join udo_tripleg as tl on (tl.id=tlc.tripLeg_id) left join udo_debrief as df on (df.tripLeg_id=tlc.tripLeg_id)
where tlc.cargo_id IN ();
