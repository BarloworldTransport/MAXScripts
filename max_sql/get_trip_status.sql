SELECT
ca.id,
ca.tripNumber,
tl.offloadingArrivalTime,
count(tlc.tripLeg_id) AS triplegs,
IF(db.debriefStartDate, 'YES', 'NO') AS is_debriefed,
IF(ca.imageGroup_id, 'YES', 'NO') AS ocr_images,
IF((ca.sysproOrderPlaced && ca.sysproOrderPlacedDate), 'YES', 'NO') AS sentToSyspro,
IF((ca.companyInvoiceNumber), 'YES', 'NO') AS isInvoiced,
(CASE
WHEN (count(tlc.tripLeg_id) > 0)
THEN (IF((tl.offloadingArrivalTime || db.debriefStartDate), 'NO', 'YES'))
ELSE
NULL
END)
as triplegDeleteable,
(CASE
WHEN (count(tlc.tripLeg_id) > 0)
THEN (IF((tl.loadingArrivalTime && tl.loadingStarted && tl.loadingFinished && tl.timeLeft && tl.offloadingArrivalTime && tl.offloadingStarted && tl.offloadingCompleted && tl.kmsBegin && tl.kmsEnd), 'YES', 'NO'))
ELSE
NULL
END)
AS tripLegCompleted,
(IF(!(ca.imageGroup_id = NULL && ca.companyInvoiceNumber = NULL && db.debriefStartDate = NULL && tl.offloadingArrivalTime = NULL), 'YES', 'NO')) AS isManuallyDeleteable,
(IF((!ca.imageGroup_id && !db.debriefStartDate && !count(tlc.tripLeg_id)), 'YES', 'NO')) AS isDeleteableByScript
FROM udo_cargo AS ca
LEFT JOIN udo_triplegcargo AS tlc ON (tlc.cargo_id=ca.id)
LEFT JOIN udo_tripleg AS tl ON (tl.id=tlc.tripLeg_id)
LEFT JOIN udo_debrief AS db ON (db.tripLeg_id=tlc.tripLeg_id)
WHERE ca.id=863508;
