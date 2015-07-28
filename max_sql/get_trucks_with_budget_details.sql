SELECT `t`.`fleetnum`, `t`.`fM` AS `fmId`, `tb`.`date`, `tb`.`kms`, `tb`.`income`/100, `t`.`licensePlate`, `tmk`.`name` AS `Make`, `tmd`.`name` AS `Model`, `f`.`name` AS `Fleet`
FROM `udo_truckbudget` AS `tb`
INNER JOIN `udo_truck` AS `t` ON (`t`.`id`=`tb`.`truck_id`)
INNER JOIN `udo_fleet` AS `f` ON (`f`.`id` = `t`.`primaryFleet_id`)
INNER JOIN `udo_make` AS `tmk` ON (`tmk`.`id`=`t`.`make_id`)
INNER JOIN `udo_model` AS `tmd` ON (`tmd`.`id`=`t`.`model_id`)
WHERE `tb`.`date` >= "2015-04-01 00:00:00"
GROUP BY `t`.`id`;