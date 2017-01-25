select tr.id as truck_id,
tr.fleetnum,
tr.licensePlate,
mk.name as makeName,
md.name as modelName,
pf.name as primaryFleetName,
tr.fM,
tr.cellnum,
tr.active,
tr.averageKmsPerDay,
gr.name as groupName,
CONCAT(p.first_name, " ", p.last_name) as contactName
from udo_truck as tr
left join permissionuser as pu on (pu.personal_group_id=tr.group_owner_group_id)
left join person as p on (p.id=pu.person_id)
left join `group` as gr on (gr.id=tr.group_owner_group_id)
left join udo_make as mk on (mk.id=tr.make_id)
left join udo_model as md on (md.id=tr.model_id)
left join udo_fleet as pf on (pf.id=tr.primaryFleet_id)
where tr.id=585;

