SELECT it.reference AS shipmentNumber, it.cargo_id FROM udo_triplegcargo AS tlc LEFT JOIN udo_importedtrip AS it ON (it.cargo_id = tlc.cargo_id) WHERE tlc.tripLeg_id = 1143557;

SELECT ca.tripNumber, ca.id as cargo_id FROM udo_triplegcargo AS tlc LEFT JOIN udo_cargo AS ca ON (ca.id = tlc.cargo_id) WHERE tlc.tripLeg_id = 1143557;
