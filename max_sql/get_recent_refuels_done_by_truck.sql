SELECT rf.id,
rfo.orderNumber,
tr.fleetnum,
rf.odo,
rf.litres,
rf.fillDateTime,
CONCAT(pc.first_name, " ", pc.last_name) as createdBy,
rf.time_created,
CONCAT(plm.first_name, " ", plm.last_name) as lastModifiedBy,
rf.time_last_modified
FROM udo_refuel AS rf
LEFT JOIN udo_refuelordernumber AS rfo ON (rfo.id=rf.refuelOrderNumber_id)
LEFT JOIN udo_truck AS tr ON (tr.id=rf.truck_id)
LEFT JOIN permissionuser AS puc ON (puc.id=rf.created_by)
LEFT JOIN person AS pc ON (pc.id=puc.person_id)
LEFT JOIN permissionuser AS pulm ON (pulm.id=rf.last_modified_by)
LEFT JOIN person AS plm ON (plm.id=pulm.person_id)
WHERE tr.fleetnum = "444032"
ORDER BY rf.fillDateTime DESC LIMIT 5\G
