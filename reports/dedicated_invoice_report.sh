#! /bin/bash

# Basic Stuff
clia DataView createItemList objectRegistry="udo_TripLeg" name="Dedicated Invoice Report" internalCreator="Report" primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="All Users" groupOwnerCrud="Read"
clia DataView setShowOptions objectRegistry="udo_TripLeg" type="ItemListDataView" name="Dedicated Invoice Report" detailAction="Update" showActions=0 showExport=1 showFooterBar=
clia DataView setIcon objectRegistry="udo_TripLeg" type="ItemListDataView" name="Dedicated Invoice Report" icon="icon-reports.gif"
clia DataView setPageSize objectRegistry="udo_TripLeg" type="ItemListDataView" name="Dedicated Invoice Report" pageSize="1000"
clia DataView setNoRecordsMessage objectRegistry="udo_TripLeg" type="ItemListDataView" name="Dedicated Invoice Report" message="No records were returned."
# Set the filter
clia DataView setFilter objectRegistry="udo_TripLeg" type="ItemListDataView" name="Income for Export no calc" filter='truck_id = @InterimResult:truck_id AND ((loadingStarted = null AND loadingArrivalETA >= @InterimResult:beginDate AND loadingArrivalETA <= @InterimResult:endDate) OR (loadingStarted != null AND loadingStarted >= @InterimResult:beginDate AND loadingStarted <= @InterimResult:endDate)) AND (workshopTrip = "false" OR workshopTrip = null)'
# Set the report order
clia DataView setOrderBy objectRegistry="udo_TripLeg" type="ItemListDataView" name="Dedicated Invoice Report" orderByField=trip_id orderByDirection=ASC
# Add fields
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Syspro Sales Order Number" source="udo_TripLegCargo.tripLeg_id->cargo_id->sysproSalesOrder"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="MAX Trip Number" source="udo_TripLegCargo.tripLeg_id->cargo_id->tripNumber"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Customer Order Number" source="udo_TripLegCargo.tripLeg_id->cargo_id->orderNumber"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Dispatched / Loaded Weight" source="udo_TripLegCargo.tripLeg_id->cargo_id->tonsActual"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Route Info" source="udo_TripLegCargo.tripLeg_id->cargo_id->rate_id->route_id"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Rate" source="udo_TripLegCargo.tripLeg_id->cargo_id->rate_id"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Total Revenue" source="udo_TripLegCargo.tripLeg_id->cargo_id->totalIncome"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Opening OD" source="kmsBegin"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Closing OD" source="kmsEnd"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Loading Started" source="loadingStarted"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Offloading Completed" source="offloadingCompleted"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Date Created" source="time_created"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Dedicated Invoice Report" label="Trip Date" source="loadingArrivalTime"
# Create the report
clia Report create name="Dedicated Invoice Report" objectRegistry=udo_TripLeg dataView="Dedicated Invoice Report" dataViewType=ItemListDataView primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Admin" groupOwnerCrud="Read"
# Set interim view
clia Report setInterimDataView name="Dedicated Invoice Report" objectRegistry=DateRangeValue dataViewName="Trucks in a Fleet" type=ItemListDataView