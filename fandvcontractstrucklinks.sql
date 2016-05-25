SELECT fvc.id, tr.id as tid, cu.tradingName, fvc.fixedContribution, fvc.fixedCost, fvc.numberOfDays, drv.value, bu.name, fvc.startDate, fvc.endDate, td.description, tr.fleetnum
FROM udo_customer as cu
LEFT JOIN udo_fandvcontract AS fvc ON (fvc.customer_id=cu.id)
LEFT JOIN daterangevalue AS drv ON (drv.objectInstanceId = fvc.variableCostRate_id AND drv.type = "Rate")
LEFT JOIN udo_businessunit AS bu ON (bu.id = fvc.businessUnit_id)
LEFT JOIN udo_rates AS ra ON (ra.id = fvc.variableCostRate_id)
LEFT JOIN udo_truckdescription AS td ON (td.id = ra.truckDescription_id)
LEFT JOIN udo_fandvcontracttruck_link AS trl ON (trl.fandVContract_id = fvc.id)
LEFT JOIN udo_truck AS tr ON (tr.id = trl.truck_id)
WHERE cu.active = 1 AND cu.primaryCustomer = 1 AND cu.useFandVContract = 1 AND fvc.startDate >= "2016-02-29 22:00" AND fvc.endDate <= "2016-03-31 21:59:59" ORDER BY cu.tradingName ASC;
