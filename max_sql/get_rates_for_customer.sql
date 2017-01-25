SELECT CONCAT(lf.name, " TO ", lt.name) AS Route,
bu.name AS `Business Unit`,
rt.name AS `Rate Type`,
lf.name AS `Location From`,
lt.name AS `Location To`,
FORMAT((drv.value / 100), 2) AS `Rate`
FROM udo_rates AS ra
LEFT JOIN udo_customer AS cu ON (cu.id=ra.objectInstanceId)
LEFT JOIN udo_businessunit AS bu ON (bu.id=ra.businessUnit_id)
LEFT JOIN udo_ratetype AS rt ON (rt.id=rateType_id)
LEFT JOIN udo_route AS ro ON (ro.id=ra.route_id)
LEFT JOIN udo_location AS lf ON (lf.id=ro.locationFrom_id)
LEFT JOIN udo_location AS lt ON (lt.id=ro.locationTo_id)
LEFT JOIN daterangevalue AS drv ON (drv.objectInstanceId=ra.id AND drv.type="Rate" AND drv.beginDate >= DATE(CONCAT(CURDATE(), ' 00:00:00')))
WHERE cu.tradingName like "Sime Darby Hudson & Knight";