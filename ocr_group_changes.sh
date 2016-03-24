!#/bin/bash
clia=/usr/local/bin/clia
# Change max group to new group => name OcrFreight
$clia updateHelper runQ "update \`group\` set name='OcrFreight' where name='max'"

# Clear cache for the group record
$clia $TENANT_ID Cache clear prefix=Group key=2603

# Remove Ocr Processor membership from parent groups
$clia User deleteMembership group="Ocr Processor" rolePlayer="Debrief Tracker"
$clia User deleteMembership group="Ocr Processor" rolePlayer="OCR Admin"
$clia User deleteMembership group="Ocr Processor" rolePlayer="Debtor Clerk"
$clia User deleteMembership group="Ocr Processor" rolePlayer="Debrief Manager"
$clia User deleteMembership group="Ocr Processor" rolePlayer="Support Admin"
$clia User deleteMembership group="Ocr Processor" rolePlayer="API User"

# Remove Ocr Processor membership from OcrFreight group
$clia User deleteMembership rolePlayer="Ocr Processor" group="OcrFreight"
$clia User deleteMembership rolePlayer="Ocr Processor" group="Ocr"
$clia User deleteMembership rolePlayer="Ocr Processor" group="Ocr Viewer"

# Delete Ocr Processor group
$clia User deleteGroup name="Ocr Processor"

# Add group for Energy Ocrs
$clia User addGroup name='OcrEnergy' parentGroup='Admin' isPersonal=0 primaryOwner='Admin' groupOwner='All Users'

# Flush cache
$clia 0 Cache flush

