# table: imagedefinition
# +----+------------------------------------------+
# | id | name                                     |
# +----+------------------------------------------+
# | 59 | Energy Total Delivery Note               |
# | 60 | Energy Bill of Lading                    |
# | 61 | Energy Total Dangerous Goods Declaration |
# | 62 | Energy Delivery Note                     |
# | 63 | Energy Trip Envelope                     |
# | 64 | Freight Delivery Note                    |
# | 65 | Energy Loading Advice                    |
# | 66 | Chep Pallet Control Note                 |
# | 67 | Freight Trip Envelope                    |
# | 68 | Energy on Road Vehicle Check             |
# +----+------------------------------------------+
SET @imagepath = "~/htdocs/mobilize/tenants/0002/data/images";
SELECT ca.id AS cargo_id, ca.tripNumber,
CONCAT(@imagepath, i.path, '/', i.id) AS img_path
FROM img AS i
LEFT JOIN ocr AS o ON (o.img_id = i.id)
LEFT JOIN imagedefinition AS imd ON (imd.id = o.img_def_id)
LEFT JOIN udo_cargo AS ca ON (ca.imageGroup_id = i.img_group_id)
WHERE imd.id = 59
ORDER BY i.id DESC LIMIT 10;


