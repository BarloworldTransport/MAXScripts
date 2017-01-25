select ra.id, cu.tradingName, lf.name as cityFrom, lt.name as cityTo from udo_rates as ra left join udo_route as ro on (ro.id=ra.route_id) left join udo_customer as cu on (cu.id=ra.objectInstanceId) left join udo_location as lf on (lf.id=ro.locationFrom_id) left join udo_location as lt on (lt.id=ro.locationTo_id) where lf.name like "%chloorkop%" AND lt.name like "%ngodwana%" AND cu.tradingName like "%chlorine%";
