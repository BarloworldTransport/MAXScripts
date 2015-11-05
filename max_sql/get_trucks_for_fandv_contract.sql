SET @fandVContract_id = 4868; SET @objreg=(SELECT ID FROM objectregistry WHERE handle LIKE "udo%f%v%contract%truck%link%");
SELECT tr.id, tr.fleetnum, DATE_ADD(drv.beginDate, INTERVAL 2 HOUR) AS beginDate, DATE_ADD(drv.endDate, INTERVAL 2 HOUR) AS endDate 
FROM udo_fandvcontracttruck_link AS fvtl
LEFT JOIN udo_truck AS tr ON (tr.id = fvtl.truck_id)
LEFT JOIN daterangevalue AS drv ON (drv.objectInstanceId=fvtl.id AND drv.objectRegistry_id=@objreg) 
WHERE fvtl.fandVContract_id = @fandVContract_id;
