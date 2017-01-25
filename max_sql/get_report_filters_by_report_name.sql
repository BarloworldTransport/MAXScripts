SELECT r.name, dv.filter as reportFilter, IF(r.interimDataView_id IS NOT NULL, idv.filter, NULL) as interumFilter
FROM report AS r
LEFT JOIN dataview AS dv ON (dv.id=r.dataView_id)
LEFT JOIN dataview AS idv ON (idv.id=r.interimDataView_id)
WHERE dv.filter LIKE "%workshopTrip = \"0\"%"\G
