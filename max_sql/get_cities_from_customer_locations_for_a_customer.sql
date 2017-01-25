select lp.name as City, lo.name as Point
from udo_customerlocations as cl
left join udo_customer as cu on (cu.id=cl.customer_id)
left join udo_location as lo on (lo.id=cl.location_id and lo._type="udo_Point")
left join udo_location as lp on (lp.id=lo.parent_id and lp._type="udo_City")
where cu.tradingName = "Glencore (Dedicated)"
group by lp.name
order by lp.name asc;

