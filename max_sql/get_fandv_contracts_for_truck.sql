SET @fleetnum = "D572"; SET @startDate = NOW();
SELECT fandv.id AS fandVContract_id, fvtl.id, cu.id as customer_id, cu.tradingName, DATE_ADD(fandv.startDate, INTERVAL 2 HOUR) AS startDate, DATE_ADD(fandv.endDate, INTERVAL 2 HOUR) AS endDate, td.description AS truckDescription
FROM udo_fandvcontracttruck_link AS fvtl
LEFT JOIN udo_fandvcontract AS fandv ON (fandv.id = fvtl.fandVContract_id)
LEFT JOIN udo_truck AS tr ON (tr.id = fvtl.truck_id)
LEFT JOIN udo_customer AS cu ON (cu.id = fandv.customer_id)
LEFT JOIN udo_rates AS ra ON (ra.id = fandv.variableCostRate_id)
LEFT JOIN udo_truckdescription AS td ON (td.id = ra.truckDescription_id)
WHERE DATE_ADD(fandv.startDate, INTERVAL 2 HOUR) >= DATE_FORMAT(@startDate ,'%Y-%m-01') AND tr.fleetnum LIKE @fleetnum
ORDER BY cu.tradingName ASC;
