SELECT gpt.ID,
gpt.condition,
obr.name,
gr.name,
tpog.name AS `template primary owner`,
gpt.template_primary_owner_crud AS `template primary owner CRUD`,
tgog.name AS `template group owner`,
gpt.template_group_owner_crud AS `template group owner CRUD`,
gpt.rule
FROM grouppermissiontemplate AS gpt
LEFT JOIN objectregistry AS obr ON (obr.ID=gpt._ObjectRegistry_id)
LEFT JOIN `group` AS gr ON (gr.ID=gpt.group_id)
LEFT JOIN `group` AS tpog ON (tpog.ID=gpt.template_primary_owner_group_id)
LEFT JOIN `group` AS tgog ON (tgog.ID=gpt.template_group_owner_group_id);
