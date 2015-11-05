# Look for order by BillOfLoading
SELECT id, xml FROM scheduledimportedtrip
WHERE xml LIKE "%ClientCode>MEAF%" AND xml LIKE "%Status>Delivered%" AND xml LIKE "%BillOfLading>TR00147986%"
ORDER BY ID DESC
LIMIT 1;

# Look for Meadow Feeds PMB orders
SELECT id, xml FROM scheduledimportedtrip
WHERE xml LIKE "%ClientCode>NCP%" AND xml LIKE "%Status>Delivered%"
ORDER BY ID DESC
LIMIT 1;

# Look for order with shipment number
SELECT id FROM scheduledimportedtrip
WHERE xml LIKE "%ClientCode>NCP%" AND xml LIKE "%Status>Accepted%" AND xml LIKE "%ShipmentNumber>257660%";
