#!/bin/bash
clia=/usr/local/bin/clia
$clia $TENANT_ID DataView createItemList objectRegistry="udo_Refuel" name="Refuel Report (by fleet) - Time Last Modified" internalCreator="Report" primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Refuel Admin" groupOwnerCrud="Read"

$clia $TENANT_ID DataView setShowOptions objectRegistry="udo_Refuel" type="ItemListDataView" name="Refuel Report (by fleet) - Time Last Modified" detailAction="Open" showActions=0 showExport=1 showFooterBar=1

$clia $TENANT_ID DataView setIcon objectRegistry="udo_Refuel" type="ItemListDataView" name="Refuel Report (by fleet) - Time Last Modified" icon="icon-reports.gif"

$clia $TENANT_ID DataView setNoRecordsMessage objectRegistry="udo_Refuel" type="ItemListDataView" name="Refuel Report (by fleet) - Time Last Modified" message=""

$clia $TENANT_ID DataView addParameterSelect objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" parameterHandle="driver" parameterName="Driver" mandatory="0" objectRegistryToSelect="udo_Driver" filter=''

$clia $TENANT_ID DataView addParameterSelect objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" parameterHandle="refuelPoint" parameterName="Refuel Point" mandatory="0" objectRegistryToSelect="udo_Point" filter='Refuel Points only'

$clia $TENANT_ID DataView setFilter objectRegistry="udo_Refuel" type="ItemListDataView" name="Refuel Report (by fleet) - Time Last Modified" filter='truck_id = @InterimResult:truck_id AND time_last_modified >= @InterimResult:beginDate AND time_last_modified <= @InterimResult:endDate AND driver_id = @Parameter:driver AND (authorized = "Unauthorized" OR point_id = @Parameter:refuelPoint)'

$clia $TENANT_ID DataView setOrderBy objectRegistry="udo_Refuel" type="ItemListDataView" name="Refuel Report (by fleet) - Time Last Modified" orderByField=refuelOrderNumber_id orderByDirection=ASC

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Fleet No." source="truck_id->fleetnum"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Truck FM ID" source="truck_id->fM"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Refuel Order Number" source="refuelOrderNumber_id"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Refuel Time" source="fillDateTime"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Driver Nickname" source="driver_id->nickname"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Driver FM ID" source="driver_id->fM"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Staff No" source="driver_id->staffNumber"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Location" source="point_id->name"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Unauthorised Location Name" source="unauthorizedLocation"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Odo" source="odo"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Litres" source="litres"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Full / Partial" source="full_or_Partial"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Cost" source="cost"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Fuel Consumption" source="fuelConsumption"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Time Created" source="time_created"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Notes" source="notes"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Time Last Edited" source="time_last_modified"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Last Edited By" source="last_modified_by"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet) - Time Last Modified" label="Created By" source="created_by"

$clia $TENANT_ID Report create name="Refuel Report (by fleet) - Time Last Modified" objectRegistry=udo_Refuel dataView="Refuel Report (by fleet) - Time Last Modified" dataViewType=ItemListDataView primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Refuel" groupOwnerCrud="Read"

$clia $TENANT_ID Report setInterimDataView name="Refuel Report (by fleet) - Time Last Modified" objectRegistry=DateRangeValue dataViewName="Trucks in a Fleet" type=ItemListDataView
