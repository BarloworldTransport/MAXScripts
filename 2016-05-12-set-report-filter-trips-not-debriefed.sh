#!/bin/bash
clia=$(which clia)
# Script was written to apply change to the Trips Not Debriefed Report filter on MAX to correct its filter results
$clia DataView setFilter objectRegistry=udo_TripLeg name='Trips Not Debriefed' type=ItemListDataView filter='driver_id = @Parameter:Driver AND udo_Debrief.tripLeg_id->documentation = "trip in process" AND ((loadingStarted != null AND loadingStarted >= @Parameter:startDate AND loadingStarted < @Parameter:stopDate) OR (loadingStarted = null AND loadingArrivalETA >= @Parameter:startDate AND loadingArrivalETA < @Parameter:stopDate))'
$clia $TENANT_ID Cache clear prefix=DataView key=827
