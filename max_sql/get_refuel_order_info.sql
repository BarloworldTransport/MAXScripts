select IF(r.id, r.id, "DELETED") as refuel_id, ron.id as refuelOrderNumber_id, ron.orderNumber from udo_refuelordernumber as ron left join udo_refuel as r on (r.refuelOrderNumber_id=ron.id) where ron.orderNumber = "445564";
