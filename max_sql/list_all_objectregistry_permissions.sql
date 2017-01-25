SELECT `objr`.`ID`,
`objr`.`handle`,
`powner`.`name` AS `primaryOwner_name`,
`objr`.`primary_owner_crud` as `primaryOwner_crud`,
`gowner`.`name` AS `groupOwner_name`,
`objr`.`group_owner_crud` AS `groupOwner_crud`
FROM `objectregistry` AS `objr`
LEFT JOIN `group` AS `gowner` ON (`gowner`.`ID`=`objr`.`group_owner_group_id`)
LEFT JOIN `group` AS `powner` ON (`powner`.`ID`=`objr`.`primary_owner_group_id`)
ORDER BY `objr`.`handle` ASC;