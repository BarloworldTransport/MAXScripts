SELECT id
FROM udo_cargo
WHERE ID IN (SELECT cargo_id FROM udo_triplegcargo WHERE LENGTH(tripLeg_id) < 7 order by id desc) AND customer_id=10258
ORDER BY id DESC LIMIT 10;

