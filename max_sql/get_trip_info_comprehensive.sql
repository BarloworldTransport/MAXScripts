select
ca.id,
ca.tripNumber,
ca.orderNumber,
ca.companyInvoiceNumber,
cu.tradingName,
t.id as truck_id,
t.fleetnum,
pc.name as product,
ca.tonsActual,
DATE_ADD(ca.plannedLoadingArrivalDate, INTERVAL 2 HOUR) as plannedLoadingArrivalDate,
DATE_ADD(ca.plannedOffloadingArrivalDate, INTERVAL 2 HOUR) as plannedOffloadingArrivalDate,
cf.name as cityFrom,
lf.name as townFrom,
ct.name as cityTo,
lt.name as townTo,
rt.name as rateType,
dr.nickname as driver,
DATE_ADD(tl.loadingArrivalETA, INTERVAL 2 HOUR) as loadingArrivalETA,
DATE_ADD(tl.offloadingArrivalETA, INTERVAL 2 HOUR) as offloadingArrivalETA,
DATE_ADD(tl.loadingArrivalTime, INTERVAL 2 HOUR) as loadingArrivalTime,
DATE_ADD(tl.loadingStarted, INTERVAL 2 HOUR) as loadingStarted,
DATE_ADD(tl.loadingFinished, INTERVAL 2 HOUR) as loadingFinished,
DATE_ADD(tl.timeLeft, INTERVAL 2 HOUR) as timeLeft,
DATE_ADD(tl.offloadingArrivalTime, INTERVAL 2 HOUR) as offloadingArrivalTime,
DATE_ADD(tl.offloadingStarted, INTERVAL 2 HOUR) as offloadingStarted,
DATE_ADD(tl.offloadingCompleted, INTERVAL 2 HOUR) as offloadingCompleted,
tl.kmsBegin,
tl.kmsEnd,
tl.subcontractor_id,
ca.sysproError,
ca.sysproOrderPlaced,
ca.sysproOrderPlacedDate,
ca.rate_id,
ca.truckDescription_id,
ca.tripCaptureCompleted,
ra.businessUnit_id as rateBU,
f.name as fleetOnPB
from udo_cargo as ca
left join udo_triplegcargo as tlc on (tlc.cargo_id=ca.id)
left join udo_tripleg as tl on (tl.id = tlc.tripLeg_id)
left join udo_truck as t on (t.id=tl.truck_id)
left join udo_fleettrucklink as ftl on (ftl.truck_id=t.id)
left join udo_fleet as f on (f.id=ftl.fleet_id)
left join daterangevalue as drv on (drv.objectInstanceId=ftl.id)
left join udo_productcategory as pc on (pc.id=ca.productCategory_id)
left join udo_location as lf on (lf.id=ca.locationFrom_id)
left join udo_location as lt on (lt.id=ca.locationTo_id)
left join udo_location as cf on (cf.id=ca.cityFrom_id)
left join udo_location as ct on (ct.id=cityTo_id)
left join udo_rates as ra on (ra.id=ca.rate_id)
left join udo_ratetype as rt on (rt.id=ra.rateType_id)
left join udo_driver as dr on (dr.id=tl.driver_id)
left join udo_customer as cu on (cu.id=ca.customer_id)
where (drv.beginDate IS NOT NULL) AND (drv.endDate IS NULL OR drv.endDate >= DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')) AND
ca.id = 968024
group by ca.id\G
