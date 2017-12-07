#! /bin/bash

# Report details
clia DataView createItemList objectRegistry="udo_TrackingLoad" name="Trackmatic Trips" internalCreator="Report" primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Manline" groupOwnerCrud="Read"
clia DataView setShowOptions objectRegistry="udo_TrackingLoad" type="ItemListDataView" name="Trackmatic Trips" detailAction="Open" showActions=0 showExport=1 showFooterBar=1
clia DataView setIcon objectRegistry="udo_TrackingLoad" type="ItemListDataView" name="Trackmatic Trips" icon="icon-reports.gif"
clia DataView setPageSize objectRegistry="udo_TrackingLoad" type="ItemListDataView" name="Trackmatic Trips" pageSize="500"
clia DataView setNoRecordsMessage objectRegistry="udo_TrackingLoad" type="ItemListDataView" name="Trackmatic Trips" message="No records were returned."
# Report Parameters
clia DataView addParameterDateTime objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" parameterHandle="startDate" parameterName="Start Date" mandatory="1"
clia DataView addParameterDateTime objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" parameterHandle="stopDate" parameterName="Stop Date" mandatory="1"
clia DataView setFilter objectRegistry="udo_TrackingLoad" type="ItemListDataView" name="Trackmatic Trips" filter='trip_id->udo_Cargo.trip_id->loadingTripLeg_id->truck_id = @InterimResult:truck_id AND trip_id->udo_Cargo.trip_id->loadingTripLeg_id->loadingStarted != null AND trip_id->udo_Cargo.trip_id->loadingTripLeg_id->loadingStarted >= @Parameter:startDate AND trip_id->udo_Cargo.trip_id->loadingTripLeg_id->loadingStarted <= @Parameter:stopDate)'
clia DataView setOrderBy objectRegistry="udo_TrackingLoad" type="ItemListDataView" name="Trackmatic Trips" orderByField=ID orderByDirection=ASC
# Report Fields
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Cargo ID" source="trip_id->udo_Cargo.ID" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Trip Number" source="trip_id->udo_Cargo.tripNumber" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Customer" source="trip_id->udo_Cargo.customer_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Driver" source="driver_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Loading Truck" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->truck_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Primary Fleet" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->truck_id->primaryFleet_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Loading ETA" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->loadingArrivalETA" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Loading Arrival" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->loadingArrivalTime" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Loading Started" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->loadingStarted" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Loading Finished" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->loadingFinished" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Time Departed" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->timeLeft" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Offloading Arrival ETA" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->offloadingArrivalETA" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Offloading Arrival" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->offloadingArrivalTime" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Offloading Started" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->offloadingStarted" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Offloading Finished" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->offloadingCompleted" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Loading Town" source="trip_id->udo_Cargo.locationFrom_id->udo_Location.ID->parent_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Loading Point" source="trip_id->udo_Cargo.locationFrom_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Offloading Town" source="trip_id->udo_Cargo.locationTo_id->udo_Location.ID->parent_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Offloading Point" source="trip_id->udo_Cargo.locationTo_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Kms Begin" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->kmsBegin" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Kms End" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->kmsEnd" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Tons Actual" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->tonsActual" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Time Created" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->time_created" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Created By" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->created_by" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Last Update Time" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->time_last_modified" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Last Updated By" source="trip_id->udo_Cargo.trip_id->loadingTripLeg_id->last_modified_by" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Trackmatic Request Sent" source="requestSent" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TrackingLoad type=ItemListDataView name="Trackmatic Trips" label="Driver Activated On" source="activated" showInRelatedWidget=0
# Make available
clia Report create name="Trackmatic Trips" objectRegistry=udo_TrackingLoad dataView="Trackmatic Trips" dataViewType=ItemListDataView primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Manline" groupOwnerCrud="Read"
clia Report setInterimDataView name="Trackmatic Trips" objectRegistry=DateRangeValue dataViewName="Trucks in a Fleet" type=ItemListDataView
