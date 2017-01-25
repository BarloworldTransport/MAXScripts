select ca.id, cf.name, ct.name, tl.emptyKMS, (select round((select(tl.kmsEnd - tl.kmsBegin)),0)) from udo_cargo as ca
left join udo_location as cf on (cf.id=ca.cityFrom_id)
left join udo_location as ct on (ct.id=ca.cityTo_id)
left join udo_customer as cu on (cu.id = ca.customer_id)
left join udo_triplegcargo as tlc on (tlc.cargo_id = ca.id)
left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id)
where ca.id IN () order by cf.name DESC, ct.name DESC;