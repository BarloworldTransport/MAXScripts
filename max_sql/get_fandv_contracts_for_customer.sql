SET @customer = "ppc%dwaalboom%f%v%";SET @url = 'https://login.max.bwtsgroup.com/DataBrowser?browsePrimaryObject=910&browsePrimaryInstance=';
SELECT fandv.id AS fandVContract_id, cu.id as customer_id, cu.tradingName, DATE_ADD(fandv.startDate, INTERVAL 2 HOUR) AS startDate, DATE_ADD(fandv.endDate, INTERVAL 2 HOUR) AS endDate, td.description AS truckDescription, CONCAT('R', FORMAT((drv.value / 100), 2)) AS rate, CONCAT('R', FORMAT((fandv.fixedCost / 100), 2)) AS fixedCost, CONCAT('R', FORMAT((fandv.fixedContribution / 100), 2)) AS contribution, CONCAT(lf.name, " TO ", lt.name) AS fandv_route
FROM udo_customer AS cu
LEFT JOIN udo_fandvcontract AS fandv ON (fandv.customer_id = cu.id)
LEFT JOIN daterangevalue AS drv ON (drv.objectInstanceId = fandv.variableCostRate_id AND drv.type = 'Rate')
LEFT JOIN udo_rates AS ra ON (ra.id = fandv.variableCostRate_id)
LEFT JOIN udo_truckdescription AS td ON (td.id = ra.truckDescription_id)
LEFT JOIN udo_fandvcontractroute_link AS fvr ON (fvr.fandVContract_id=fandv.id)
LEFT JOIN udo_route AS ro ON (ro.id=fvr.route_id)
LEFT JOIN udo_location AS lf ON (lf.id=ro.locationFrom_id)
LEFT JOIN udo_location AS lt ON (lt.id=ro.locationTo_id)
WHERE DATE_ADD(fandv.startDate, INTERVAL 2 HOUR) >= DATE_FORMAT(NOW() ,'%Y-%m-01') AND cu.tradingName LIKE @customer GROUP BY fandv.id ORDER BY fandv.startDate;
SELECT @url;
