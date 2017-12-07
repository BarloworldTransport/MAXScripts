#! /bin/bash

# without difference fields
clia DataView createItemList objectRegistry="udo_Truck" name="Highest KMs (by fleet) -  no diffs" internalCreator="Report" primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Refuel" groupOwnerCrud="Read"
clia DataView setShowOptions objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) -  no diffs" detailAction="Open" showActions=0 showExport=1 showFooterBar=1
clia DataView setIcon objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) -  no diffs" icon="icon-reports.gif"
clia DataView setPageSize objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) -  no diffs" pageSize="100"
clia DataView setNoRecordsMessage objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) -  no diffs" message="No trucks in the fleet"
clia DataView setFilter objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) -  no diffs" filter='id = @InterimResult:truck_id'
clia DataView setOrderBy objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) -  no diffs" orderByField=fleetnum orderByDirection=ASC
clia DataView addField objectRegistry=udo_Truck type=ItemListDataView name="Highest KMs (by fleet) -  no diffs" label="Fleet No." source="fleetnum" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_Truck type=ItemListDataView name="Highest KMs (by fleet) -  no diffs" label="Month Highest Odo (Trip Leg)" source="monthHighestOdoTripLeg" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_Truck type=ItemListDataView name="Highest KMs (by fleet) -  no diffs" label="Month Highest Odo (Refuel)" source="monthHighestOdoRefuel" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_Truck type=ItemListDataView name="Highest KMs (by fleet) -  no diffs" label="Previous Month Highest Odo (Trip Leg)" source="previousMonthHighestOdoTripLeg" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_Truck type=ItemListDataView name="Highest KMs (by fleet) -  no diffs" label="Previous Month Highest Odo (Refuel)" source="previousMonthHighestOdoRefuel" showInRelatedWidget=0
clia Report create name="Highest KMs (by fleet) -  no diffs" objectRegistry=udo_Truck dataView="Highest KMs (by fleet) -  no diffs" dataViewType=ItemListDataView primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Refuel" groupOwnerCrud="Read"
clia Report setInterimDataView name="Highest KMs (by fleet) -  no diffs" objectRegistry=DateRangeValue dataViewName="Trucks in a Fleet for Month" type=ItemListDataView


# Just for selected month
clia DataView createItemList objectRegistry="udo_Truck" name="Highest KMs (by fleet) - selected date only" internalCreator="Report" primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Refuel" groupOwnerCrud="Read"
clia DataView setShowOptions objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) - selected date only" detailAction="Open" showActions=0 showExport=1 showFooterBar=1
clia DataView setIcon objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) - selected date only" icon="icon-reports.gif"
clia DataView setPageSize objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) - selected date only" pageSize="100"
clia DataView setNoRecordsMessage objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) - selected date only" message="No trucks in the fleet"
clia DataView setFilter objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) - selected date only" filter='id = @InterimResult:truck_id'
clia DataView setOrderBy objectRegistry="udo_Truck" type="ItemListDataView" name="Highest KMs (by fleet) - selected date only" orderByField=fleetnum orderByDirection=ASC
clia DataView addField objectRegistry=udo_Truck type=ItemListDataView name="Highest KMs (by fleet) - selected date only" label="Fleet No." source="fleetnum" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_Truck type=ItemListDataView name="Highest KMs (by fleet) - selected date only" label="Month Highest Odo (Trip Leg)" source="monthHighestOdoTripLeg" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_Truck type=ItemListDataView name="Highest KMs (by fleet) - selected date only" label="Month Highest Odo (Refuel)" source="monthHighestOdoRefuel" showInRelatedWidget=0
clia Report create name="Highest KMs (by fleet) - selected date only" objectRegistry=udo_Truck dataView="Highest KMs (by fleet) - selected date only" dataViewType=ItemListDataView primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Refuel" groupOwnerCrud="Read"
clia Report setInterimDataView name="Highest KMs (by fleet) - selected date only" objectRegistry=DateRangeValue dataViewName="Trucks in a Fleet for Month" type=ItemListDataView