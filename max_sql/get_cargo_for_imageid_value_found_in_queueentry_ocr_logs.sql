set @imageId=2649711;
SELECT ca.id AS 'Cargo ID'
FROM ocr AS o
LEFT JOIN img AS i ON (i.id = o.img_id)
LEFT JOIN udo_cargo AS ca ON (ca.imageGroup_id = i.img_group_id)
WHERE o.id = @imageId;
