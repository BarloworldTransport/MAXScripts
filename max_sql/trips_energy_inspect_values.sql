select tlc.cargo_id, ca.tripNumber, ca.companyInvoiceNumber, bu.name as businessUnit, tlc.tripLeg_id as salesOrder, t.fleetnum, tl.loadingArrivalTime, ca.sysproError, ca.sysproOrderPlaced, ca.sysproOrderPlacedDate from udo_triplegcargo as tlc left join udo_cargo as ca on (ca.id=tlc.cargo_id) left join udo_tripleg as tl on (tl.id=tlc.tripLeg_id) left join udo_truck as t on (t.id=tl.truck_id) left join udo_businessunit as bu on (bu.id=ca.businessUnit_id) where ca.tripNumber IN ("F28678","F25540","F25000","F25324","F27671","F26976","F27579","F28624","F23420","F27618","F22927","F27654","F27527","F23238","F0253");