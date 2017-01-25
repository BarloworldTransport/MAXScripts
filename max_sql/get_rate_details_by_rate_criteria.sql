SELECT
ra.id AS rate_id,
bu.name AS buName,
cu.id AS customer_id,
cu.tradingName AS customerName,
ro.id AS route_id, CONCAT(lf.name, ' TO ', lt.name) AS routeName,
td.id AS truckType_id,
td.description AS truckType,
rt.id AS rateType_id,
rt.name AS rateType,
ra.model
FROM udo_rates AS ra
LEFT JOIN udo_businessunit AS bu ON (bu.id=ra.businessUnit_id)
LEFT JOIN udo_customer AS cu ON (cu.id=ra.objectInstanceId)
LEFT JOIN udo_route AS ro ON (ro.id=ra.route_id)
LEFT JOIN udo_location AS lf ON (lf.id=ro.locationFrom_id)
LEFT JOIN udo_location AS lt ON (lt.id=ro.locationTo_id)
LEFT JOIN udo_truckdescription AS td ON (td.id=ra.truckDescription_id)
LEFT JOIN udo_ratetype AS rt ON (rt.id=ra.rateType_id)
WHERE ra.objectInstanceId=8025 AND ra.route_id=16716 AND truckDescription_id=21 AND rateType_id=6 AND model='Energy (tankers)';
