#! /bin/bash

# Change the filter so that it works off of loading Started if available
clia DataView setFilter objectRegistry="udo_Cargo" type="ItemListDataView" name="Trips per Customer" filter='(udo_TripLegCargo.cargo_id->tripLeg_id->workshopTrip = "0" OR udo_TripLegCargo.cargo_id->tripLeg_id->workshopTrip = null) AND customer_id = @Parameter:customer AND businessUnit_id IN @Parameter:businessUnit_id AND ((udo_TripLegCargo.cargo_id->tripLeg_id->loadingStarted != null AND udo_TripLegCargo.cargo_id->tripLeg_id->loadingStarted >= @Parameter:startDate AND udo_TripLegCargo.cargo_id->tripLeg_id->loadingStarted <= @Parameter:stopDate) OR (udo_TripLegCargo.cargo_id->tripLeg_id->loadingStarted = null AND udo_TripLegCargo.cargo_id->tripLeg_id->loadingArrivalETA >= @Parameter:startDate AND udo_TripLegCargo.cargo_id->tripLeg_id->loadingArrivalETA <= @Parameter:stopDate))'
# Add the grouping so that it groups on the cargoID
clia Report setGroupBy name='Trips per Customer' newGroupBy=id