SELECT * FROM queueentry
WHERE payload LIKE "%xml%" AND queue = "importedtrip" AND status = 'Queued' AND time_created >= "2016-04-05 10:00:00" and time_created <= "2016-04-05 11:00:00"
ORDER BY ID DESC\G
