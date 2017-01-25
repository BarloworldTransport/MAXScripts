SELECT ca.id, ca.tripNumber, dbf.*
FROM udo_cargo AS ca
LEFT JOIN udo_triplegcargo AS tlc ON (tlc.cargo_id = ca.id)
LEFT JOIN udo_debrief AS dbf ON (dbf.tripLeg_id = tlc.tripLeg_id)
WHERE ca.tripNumber IN ("F37475","F37478","F37473","F37474","F36885","F36883","F36889","F36891","F36892","F36893","F36894","F36899","F36901","F37962","F37964","F36751","F36752","F37963","F36773","F36774","F36778","F36777","F36792","F36791");
