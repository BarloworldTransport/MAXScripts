# Look for order by BillOfLoading
SELECT id, xml FROM scheduledimportedtrip
WHERE xml LIKE "%ClientCode>MEAF%" AND xml LIKE "%Status>Delivered%" AND xml LIKE "%BillOfLading>TR00147986%"
ORDER BY ID DESC
LIMIT 1;

# Look for Meadow Feeds PMB orders
SELECT payload FROM queueentry
WHERE payload LIKE "%xml%" AND payload LIKE "%ClientCode>MEAF%" AND payload LIKE "%ShipmentNumber>PA%" AND payload LIKE "%Status>Delivered%" AND queue = "importedtrip"
ORDER BY ID DESC
LIMIT 5;

# Look for order with shipment number
SELECT * FROM queueentry
WHERE payload LIKE "%xml%" AND payload LIKE "%ClientCode>MEAF%" AND payload LIKE "%ShipmentNumber>RFT974898%" AND queue = "importedtrip"
ORDER BY ID DESC\G

# Get all orders with shipment number
SELECT * FROM queueentry
WHERE payload LIKE "%ClientCode>MEAF%" AND payload LIKE "%ShipmentNumber>Delivered%" AND queue = "importedtrip" AND status = 'Done'
ORDER BY ID DESC\G
