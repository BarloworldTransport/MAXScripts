select lp.name, cul.type
INTO OUTFILE '/tmp/customerlocations001.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
LINES TERMINATED BY '\n'
from udo_customerlocations as cul
left join udo_customer as cu on (cu.id=cul.customer_id)
left join udo_location as lp on (lp.id=cul.location_id)
where cu.tradingName = "Total Sishen Mines CPL";

