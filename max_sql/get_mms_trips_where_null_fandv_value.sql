/* Check for MMS trips that are affected by no fandvcontract_id. Can also be determined by no city locations in udo_cargo and NULL udo_fandvcontract value when customer is F&V
Have to find trips by using udo_importedtrip as the primary table and left join the other tables to it */
SELECT `ca`.`ID` AS cargo_id
FROM `udo_importedtrip` AS `it`
LEFT JOIN `udo_triplegcargo` AS `tlc` ON (`tlc`.`cargo_id` = `it`.`cargo_id`)
LEFT JOIN `udo_cargo` AS `ca` ON (`ca`.`ID` = `it`.`cargo_id`)
LEFT JOIN `udo_tripleg` AS `tl` ON (`tl`.`ID` = `tlc`.`tripLeg_id`)
LEFT JOIN `udo_customer` AS `cu` ON (`cu`.`ID` = `ca`.`customer_id`)
WHERE `ca`.`cityFrom_id` IS NULL AND `ca`.`cityTo_id` IS NULL AND `ca`.`fandVContract_id` IS NULL AND `cu`.`useFandVContract` AND `cu`.`primaryCustomer` = 1 AND `cu`.`active` = 1 AND ((`tl`.`workshopTrip` = 0 OR `tl`.`workshopTrip` IS NULL) AND `tl`.`ID` IS NOT NULL AND `tl`.`hollowTrip` = 0)
ORDER BY `ca`.`ID` DESC;
