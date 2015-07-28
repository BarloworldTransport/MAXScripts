select ro.id as ID, lf.name as locationFrom, lt.name as locationTo, ro.expectedKms, ra.leadKms from udo_rates as ra left join udo_route as ro on (ro.id=ra.route_id) left join udo_location as lf on (lf.id=ro.locationFrom_id) left join udo_location as lt on (lt.id=ro.locationTo_id) where (ro.expectedKms = 0 OR ISNULL(ro.expectedKms)) AND NOT(ra.leadKms = 0 OR ISNULL(ra.leadKms));

INTO OUTFILE '/tmp/t24_routes04.csv'
