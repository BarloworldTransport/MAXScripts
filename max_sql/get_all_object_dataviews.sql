select dv.ID, dv._type,
dv.name as dv_name,
obr.handle as ob_handle,
obr.name as ob_name,
dv.filter,
ogp.name as primary_owner,
dv.primary_owner_crud,
ggp.name as group_owner,
dv.group_owner_crud
from dataview as dv
left join objectregistry as obr on (obr.id=dv.objectregistry_id)
left join `group` as ogp on (ogp.id=dv.primary_owner_group_id)
left join `group` as ggp on (ggp.id=dv.group_owner_group_id);