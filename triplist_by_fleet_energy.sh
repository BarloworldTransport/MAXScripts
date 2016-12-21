#! /bin/bash

# 2016-10-03
clia DataView createItemList objectRegistry="udo_TripLeg" name="Triplist by Fleet - Energy" internalCreator="Report" primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="BU - Energy" groupOwnerCrud="Read"
clia DataView setShowOptions objectRegistry="udo_TripLeg" type="ItemListDataView" name="Triplist by Fleet - Energy" detailAction="Open" showActions=0 showExport=1 showFooterBar=1
clia DataView setIcon objectRegistry="udo_TripLeg" type="ItemListDataView" name="Triplist by Fleet - Energy" icon="icon-reports.gif"
clia DataView setPageSize objectRegistry="udo_TripLeg" type="ItemListDataView" name="Triplist by Fleet - Energy" pageSize="6000"
clia DataView setNoRecordsMessage objectRegistry="udo_TripLeg" type="ItemListDataView" name="Triplist by Fleet - Energy" message=""
clia DataView setFilter objectRegistry="udo_TripLeg" type="ItemListDataView" name="Triplist by Fleet - Energy" filter='truck_id = @InterimResult:truck_id AND ((loadingStarted = null AND loadingArrivalETA >= @InterimResult:beginDate AND loadingArrivalETA <= @InterimResult:endDate) OR (loadingStarted != null AND loadingStarted >= @InterimResult:beginDate AND loadingStarted <= @InterimResult:endDate)) AND (workshopTrip = "0" OR workshopTrip = null) AND (hollowTrip = "0" OR hollowTrip = null)'
clia DataView setOrderBy objectRegistry="udo_TripLeg" type="ItemListDataView" name="Triplist by Fleet - Energy" orderByField=truck_id orderByDirection=ASC
clia DataView setOrderBy objectRegistry="udo_TripLeg" type="ItemListDataView" name="Triplist by Fleet - Energy" orderByField=id orderByDirection=ASC
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Trip Number" source="udo_TripLegCargo.tripLeg_id->cargo_id->tripNumber" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Cargo ID" source="cargo_id->id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Order Number" source="cargo_id->orderNumber" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Customer" source="cargo_id->customer_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Pre-loading Driver" source="preLoadingDriver_id->person_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Driver Detail" source="driver_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="OCR?" source="udo_TripLegCargo.tripLeg_id->cargo_id->hasImageGroup" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Loading Truck" source="truck_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Fleet" source="truck_id->primaryFleet_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Trailer" source="trailer_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Income" source="udo_TripLegCargo.tripLeg_id->cargo_id->totalIncome" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Trip Leg Income" source="tripLegIncome" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Loading ETA" source="loadingArrivalETA" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Loading Arrival" source="loadingArrivalTime" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Loading Started" source="loadingStarted" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Loading Finished" source="loadingFinished" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Time Departed" source="timeLeft" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Offloading Arrival ETA" source="offloadingArrivalETA" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Offloading Arrival" source="offloadingArrivalTime" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Offloading Started" source="offloadingStarted" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Offloading Finished" source="offloadingCompleted" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Loading Town" source="locationFromPoint_id->parent_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Loading Point" source="locationFromPoint_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Location To Town" source="locationToPoint_id->parent_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Offloading Point" source="locationToPoint_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Kms Begin" source="kmsBegin" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Kms End" source="kmsEnd" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Product Category" source="cargo_id->productCategory_id->name" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Product Type" source="cargo_id->productType_id->name" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Litres Invoiceable" source="cargo_id->litresInvoiceable" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Litres Loaded" source="cargo_id->litresLoaded" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Litres Offloaded" source="cargo_id->litresOffloaded" showInRelatedWidget=0
clia Report create name="Triplist by Fleet - Energy" objectRegistry=udo_TripLeg dataView="Triplist by Fleet - Energy" dataViewType=ItemListDataView primaryOwner="Justin Ward" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Manline" groupOwnerCrud="Read"
clia Report setInterimDataView name="Triplist by Fleet - Energy" objectRegistry=DateRangeValue dataViewName="Trucks in a Fleet" type=ItemListDataView

# 2016-10-04
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Created By" source="cargo_id->created_by" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet - Energy" label="Last Modified By" source="last_modified_by" showInRelatedWidget=0
