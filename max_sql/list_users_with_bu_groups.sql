select pu.id as person_id, pu.personal_group_id, p.first_name, p.last_name, gr.name, grl.id 
INTO OUTFILE '/tmp/users2.csv'
FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
LINES TERMINATED BY '\n'
from group_role_link as grl left join `group` as gr on (gr.id=grl.group_id) left join permissionuser as pu on (pu.personal_group_id=grl.played_by_group_id) left join person as p on (p.id=pu.person_id) where grl.group_id IN (2985,2986,2987,2988,3049,3098);

select CONCAT_WS(" ", p.first_name, p.last_name) as name from permissionuser as pu left join person as p on (p.id=pu.person_id) left join group_role_link as grl on (grl.played_by_group_id=pu.personal_group_id) where ISNULL(grl.group_id IN (2985,2986,2987,2988,3049,3098));

select CONCAT_WS(" ", p.first_name, p.last_name) as name from permissionuser as pu left join person as p on (p.id=pu.person_id) where pu.status = "Enabled";

select played_by_group_id from group_role_link where played_by_group_id IN (select personal_group_id from permissionuser where status = "Enabled") and ISNULL(group_id = 2986);
