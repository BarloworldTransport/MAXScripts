!#/bin/bash
clia=/usr/local/bin/clia
clia

# Remove Ocr Processor membership from parent groups
$clia User deleteMembership group="Ocr Processor" rolePlayer="Debrief Tracker"
$clia User deleteMembership group="Ocr Processor" rolePlayer="OCR Admin"
$clia User deleteMembership group="Ocr Processor" rolePlayer="Debtor Clerk"
$clia User deleteMembership group="Ocr Processor" rolePlayer="Debrief Manager"
$clia User deleteMembership group="Ocr Processor" rolePlayer="Support Admin"
$clia User deleteMembership group="Ocr Processor" rolePlayer="API User"

# Remove Ocr Processor membership from OcrFreight group
$clia User deleteMembership rolePlayer="Ocr Processor" group="max"
$clia User deleteMembership rolePlayer="Ocr Processor" group="Ocr"
$clia User deleteMembership rolePlayer="Ocr Processor" group="Ocr Viewer"

# Delete Ocr Processor group
$clia User deleteGroup name="Ocr Processor"

# Add group for Energy Ocrs
$clia User addGroup name='OcrFrieght' parentGroup='Admin' isPersonal=0 primaryOwner='Admin' groupOwner='All Users'
$clia User addGroup name='OcrEnergy' parentGroup='Admin' isPersonal=0 primaryOwner='Admin' groupOwner='All Users'

$clia User addMembership group='OcrFreight' rolePlayer='Wendy Mgaga'
$clia User addMembership group='OcrEnergy' rolePlayer='Jevaun Maria Jones'

# Flush cache
$clia 0 Cache flush

