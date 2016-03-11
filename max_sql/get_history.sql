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
