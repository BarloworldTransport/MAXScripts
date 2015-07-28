select r.id, t.id as truck_id, t.fleetnum, r.refuelOrderNumber_id, ron.orderNumber from udo_refuelordernumber as ron left join udo_refuel as r on (r.refuelOrderNumber_id=ron.id) left join udo_truck as t on (t.id=r.truck_id) where ron.orderNumber IN (366657, 367146);

