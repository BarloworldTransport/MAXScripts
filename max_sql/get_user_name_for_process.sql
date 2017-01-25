select p.first_name, p.last_name from `process` as pr left join permissionuser as pu on (pu.id=pr.created_by) left join person as p on (p.id=pu.person_id) where pr.id IN ();
