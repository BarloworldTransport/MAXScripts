SELECT * FROM `objectinstancelog`
WHERE `action`='Create' AND `objectregistry_id`=1005 ORDER BY `ID` DESC LIMIT 1\G

SELECT * FROM `objectinstancelog`
WHERE `objectregistry_id`=1005  AND `value` LIKE "%RFT973236%"\G

SELECT * FROM `objectinstancelog`
WHERE `objectregistry_id`=477  AND `value` LIKE "%RFT973237%"\G

SELECT * FROM `objectinstancelog`
WHERE `objectregistry_id`=484  AND `objectInstanceId` = 1117538\G

SELECT DISTINCT `action` FROM `objectinstancelog`
WHERE `objectregistry_id`=1005;

# Find MMS order history
SELECT * FROM `objectinstancelog` WHERE `objectregistry_id`=1038 AND `action` = "Update" AND value LIKE "%ShipmentNumber>RFT970040%" order by id desc LIMIT 10\G
