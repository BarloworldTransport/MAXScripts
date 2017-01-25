select `pg`.`name` as `playedByGroupName`, CONCAT(`p`.`first_name`, ' ', `p`.`last_name`) as fullnames, `p`.`email`, `p`.`jobTitle`, `p`.`company`, IF(`p`.`address_line_1` IS NOT NULL, `p`.`address_line_1`, NULL) as `Address`
from `group_role_link` as `grl`
left join `group` as `g` on (`g`.`id` = `grl`.`group_id`)
left join `group` as `pg` on (`pg`.`id` = `grl`.`played_by_group_id`)
left join `permissionuser` as `pu` on (`pu`.`personal_group_id` = `pg`.`id` and `pu`.`status` != 'Disabled')
left join `person` as `p` on (`p`.`id` = `pu`.`person_id`)
where `g`.`name` = 'Operations Administrator';