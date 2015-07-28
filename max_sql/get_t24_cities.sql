select lo.id, lo.name, pl.name as parentCity INTO OUTFILE '/tmp/t24_cities1.csv'
FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
LINES TERMINATED BY '\n'
FROM udo_location as lo left join udo_location as pl on (pl.id=lo.parent_id) where lo._type="udo_City";

select lo.id, lo.name, pl.name as parentCity FROM udo_location as lo left join udo_location as pl on (pl.id=lo.parent_id) where lo._type="udo_City";
