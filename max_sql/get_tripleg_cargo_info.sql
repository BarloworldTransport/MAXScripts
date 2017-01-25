select t.id as truck_id, t.fleetnum as fleetnum, f.name as primaryFleet, tlc.id as tlc_id, tl.id as tl_id, ca.id as ca_id, tl.subcontractor_id, ca.fandVContract_id as contract_id, ca.rate_id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) left join udo_truck as t on (t.id=tl.truck_id) left join udo_fleet as f on (f.id=t.primaryFleet_id) where ca.id in ();

select t.id as truck_id, t.fleetnum as fleetnum, f.name as primaryFleet, tlc.id as tlc_id, tl.id as tl_id, ca.id as ca_id, tl.subcontractor_id, ca.fandVContract_id as contract_id, ca.rate_id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) left join udo_truck as t on (t.id=tl.truck_id) left join udo_fleet as f on (f.id=t.primaryFleet_id) where ca.tripNumber in ("");

select t.id as truck_id, t.fleetnum as fleetnum, f.name as primaryFleet, tlc.id as tlc_id, tl.id as tl_id, ca.id as ca_id, tl.subcontractor_id, ca.fandVContract_id as contract_id, ca.rate_id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) left join udo_truck as t on (t.id=tl.truck_id) left join udo_fleet as f on (f.id=t.primaryFleet_id) where ca.tripNumber in ("NCP2689");

select t.id as truck_id, t.fleetnum as fleetnum, f.name as primaryFleet, tlc.id as tlc_id, tl.id as tl_id, ca.id as ca_id, tl.subcontractor_id, ca.fandVContract_id as contract_id, ca.rate_id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) left join udo_truck as t on (t.id=tl.truck_id) left join udo_fleet as f on (f.id=t.primaryFleet_id) where ca.id in (495931);


select ca.id as ca_id, tlc.id as tlc_id, tl.id as tl_id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) left join udo_truck as t on (t.id=tl.truck_id) left join udo_fleet as f on (f.id=t.primaryFleet_id) where ca.customer_id =16402 and tl.loadingArrivalETA >= "2014-03-01 00:00:00" and tl.loadingArrivalETA <= "2014-05-01 00:00:00";

select t.id as truck_id, t.fleetnum as fleetnum, f.name as primaryFleet, tlc.id as tlc_id, tl.id as tl_id, ca.id as ca_id, tl.subcontractor_id, ca.fandVContract_id as contract_id, ca.rate_id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) left join udo_truck as t on (t.id=tl.truck_id) left join udo_fleet as f on (f.id=t.primaryFleet_id) where ca.fandVContract_id=1132 and tl.truck_id=1208;


select tlc.id as tlc_id, tl.id as tl_id, ca.id as ca_id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) where ca.id=531291;

select ca.id, tl.truck_id, ca.customer_id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) where ca.tripNumber="388882";

select ca.id, tl.truck_id, ca.customer_id from udo_cargo as ca left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id) left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id) where tlc.tripLeg_id=659246;
