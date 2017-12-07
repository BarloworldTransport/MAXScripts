#! /bin/bash

# Original filter
# clia DataView setFilter objectRegistry="udo_TripLeg" type="ItemListDataView" name="Empty Kms Report" filter='businessUnit_id IN @Parameter:businessUnit_id AND (loadingArrivalETA >= @Parameter:startDate AND loadingArrivalETA <= @Parameter:stopDate)'

# Updated filter
clia DataView setFilter objectRegistry="udo_TripLeg" type="ItemListDataView" name="Empty Kms Report" filter='businessUnit_id IN @Parameter:businessUnit_id AND ((loadingStarted != null AND loadingStarted >= @Parameter:startDate AND loadingStarted <= @Parameter:stopDate) OR (loadingStarted = null AND loadingArrivalETA >= @Parameter:startDate AND loadingArrivalETA <= @Parameter:stopDate))'