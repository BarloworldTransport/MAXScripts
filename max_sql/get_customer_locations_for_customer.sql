select lo.name from udo_customerlocations as cl left join udo_customer as cu on (cu.tradingName="Hans Merensky") left join udo_location as lo on (lo._type="udo_Plantation") where cl.location_id IN (lo.id) and cl.customer_id = cu.id;

