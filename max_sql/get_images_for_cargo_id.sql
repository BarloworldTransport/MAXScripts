SET @imagepath = "~/htdocs/mobilize/tenants/0002/data/images";
SELECT o.id AS ocr_id, i.id AS img_id, imd.id AS img_def_id, imd.name AS img_def_name, ca.id AS cargo_id, ca.tripNumber,
CONCAT(@imagepath, i.path, '/', i.id) AS img_path
FROM udo_cargo AS ca
LEFT JOIN img AS i ON (i.img_group_id = ca.imageGroup_id)
LEFT JOIN ocr AS o ON (o.img_id = i.id)
LEFT JOIN imagedefinition AS imd ON (imd.id = o.img_def_id)
WHERE ca.id = 1184836;
