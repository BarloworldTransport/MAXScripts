#!/bin/bash
# udo_TripLeg -> 2861 - workshopTrip
# udo_TripLeg -> 11408 - hollowTrip
clia=$(which clia)

# Report: Triplist by Fleet
$clia DataView setFilter objectRegistry="udo_TripLeg" name="Triplist by Fleet" type="ItemListDataView" filter="truck_id = @InterimResult:truck_id AND ( ( 2848 = null AND 2845 >= @InterimResult:beginDate AND 2845 <= @InterimResult:endDate ) OR ( 2848 != null AND 2848 >= @InterimResult:beginDate AND 2848 <= @InterimResult:endDate ) ) AND (2861 = \"0\" OR 2861 = null) AND (11408 = \"0\" OR 11408 = null)"

# Report: Triplist by Fleet (T24)
$clia DataView setFilter objectRegistry="udo_TripLeg" name="Triplist by Fleet (T24)" type="ItemListDataView" filter="udo_tripleg.truck_id = @InterimResult:truck_id AND ((udo_tripleg.loadingStarted = null AND udo_tripleg.loadingArrivalETA >= @InterimResult:beginDate AND udo_tripleg.loadingArrivalETA <= @InterimResult:endDate) OR (udo_tripleg.loadingStarted != null AND udo_tripleg.loadingStarted >= @InterimResult:beginDate AND udo_tripleg.loadingStarted <= @InterimResult:endDate)) AND 2848 <= @InterimResult:endDate ) ) AND (2861 = \"0\" OR 2861 = null) AND (11408 = \"0\" OR 11408 = null)"
