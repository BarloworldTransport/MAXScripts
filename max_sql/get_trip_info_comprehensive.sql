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
cf.id AS cityFromID,
cf.name as cityFrom,
lf.name as townFrom,
ct.id as cityToID,
ct.name as cityTo,
lt.name as townTo,
rt.name as rateType,
ra.route_id,
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
f.name as fleetOnPB,
CONCAT(catc.first_name, " ", catc.last_name) as cargo_created_by,
ca.time_created,
CONCAT(calm.first_name, " ", calm.last_name) as cargo_last_modified_by,
ca.time_last_modified,
CONCAT(tltc.first_name, " ", tltc.last_name) as tripleg_created_by,
tl.time_created,
CONCAT(tllm.first_name, " ", tllm.last_name) as tripleg_last_modified_by,
tl.time_last_modified
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
left join permissionuser as caputc on (caputc.id=ca.created_by)
left join permissionuser as capulm on (capulm.id=ca.last_modified_by)
left join permissionuser as tlputc on (tlputc.id=tl.created_by)
left join permissionuser as tlpulm on (tlpulm.id=tl.last_modified_by)
left join person as catc on (catc.id=caputc.person_id)
left join person as calm on (calm.id=capulm.person_id)
left join person as tltc on (tltc.id=tlputc.person_id)
left join person as tllm on (tllm.id=tlpulm.person_id)
where (drv.beginDate IS NOT NULL) AND (drv.endDate IS NULL OR drv.endDate >= DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')) AND
ca.id = 1089381
group by ca.id\G
