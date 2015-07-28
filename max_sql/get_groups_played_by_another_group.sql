select g.name as groupName, pg.name as playedByGroupName
from group_role_link as grl
left join `group` as g on (g.id=grl.group_id)
left join `group` as pg on (pg.id=grl.played_by_group_id)
where g.name ="WorkshopAdmin";
