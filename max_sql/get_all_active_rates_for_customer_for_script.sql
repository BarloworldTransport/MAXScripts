# Get all rates for a customer and put into a specified format for internal automation script
select cu.tradingName as customer,
pf.name as provinceFrom,
lf.name as locationFrom,
pt.name as provinceTo,
lt.name as locationTo,
rt.name as rateType,
drvRate.value as rate,
drvED.value as expectedDistance,
drvEmpty.value as expectedEmptyKms,
drvDPT.value as daysPerTrip,
drvDPM.value as daysPerMonth,
td.description as truckDescription,
ra.leadKms,
ra.minimumTons,
ra.model as contributionModel,
bu.name as businessUnit,
fvl.name as fleetValue,
drvFC.value as fuelConsumption
INTO OUTFILE '/tmp/hillside_rates.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
LINES TERMINATED BY '\n'
FROM udo_rates as ra
left join udo_customer as cu on (cu.id=ra.objectInstanceId)
left join udo_route as ro on (ro.id=ra.route_id)
left join udo_location as lf on (lf.id=ro.locationFrom_id)
left join udo_location as lt on (lt.id=ro.locationTo_id)
left join udo_location as pf on (pf.id=lf.parent_id)
left join udo_location as pt on (pt.id=lt.parent_id)
left join udo_truckdescription as td on (td.id=ra.truckDescription_id)
left join udo_businessunit as bu on (bu.id=ra.businessUnit_id)
left join udo_ratetype as rt on (rt.id=ra.rateType_id)
left join daterangevalue as drvRate on (drvRate.objectInstanceId=ra.id and drvRate.type="Rate" and drvRate.endDate IS NULL)
left join daterangevalue as drvED on (drvED.objectInstanceId=ra.id and drvED.type="ExpectedDistance" and drvRate.endDate IS NULL)
left join daterangevalue as drvEmpty on (drvEmpty.objectInstanceId=ra.id and drvEmpty.type="ExpectedEmptyKms" and drvRate.endDate IS NULL)
left join daterangevalue as drvDPT on (drvDPT.objectInstanceId=ra.id and drvDPT.type="DaysPerTrip" and drvRate.endDate IS NULL)
left join daterangevalue as drvDPM on (drvDPM.objectInstanceId=ra.id and drvDPM.type="DaysPerMonth" and drvRate.endDate IS NULL)
left join daterangevalue as drvFV on (drvFV.objectInstanceId=ra.id and drvFV.type="Fleet" and drvRate.endDate IS NULL)
left join daterangevalue as drvFC on (drvFC.objectInstanceId=ra.id and drvFC.type="FuelConsumptionForRoute" and drvRate.endDate IS NULL)
left join udo_fleet as fvl on (fvl.id=drvFV.value)
where ra.enabled=1 and cu.active = 1 and cu.primaryCustomer = 1 and cu.useFandVContract = 0 and cu.tradingName IN ("Hillside Aluminium Ltd") group by ra.id order by cu.tradingName;

