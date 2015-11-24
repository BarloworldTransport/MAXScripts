#!/bin/bash
clia=/usr/local/bin/clia
$clia $TENANT_ID DataView createItemList objectRegistry="udo_Refuel" name="Refuel Report (by truck) - Time Last Modified" internalCreator="Report" primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Refuel Admin" groupOwnerCrud="Read"

$clia $TENANT_ID DataView setShowOptions objectRegistry="udo_Refuel" type="ItemListDataView" name="Refuel Report (by truck) - Time Last Modified" detailAction="Open" showActions=0 showExport=1 showFooterBar=

$clia $TENANT_ID DataView setIcon objectRegistry="udo_Refuel" type="ItemListDataView" name="Refuel Report (by truck) - Time Last Modified" icon="icon-reports.gif"

$clia $TENANT_ID DataView setNoRecordsMessage objectRegistry="udo_Refuel" type="ItemListDataView" name="Refuel Report (by truck) - Time Last Modified" message=""

$clia $TENANT_ID DataView addParameterSelect objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Time Last Modified" parameterHandle="driver" parameterName="Driver" mandatory="0" objectRegistryToSelect="udo_Driver" filter=''

$clia $TENANT_ID DataView addParameterSelect objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Time Last Modified" parameterHandle="refuelPoint" parameterName="Refuel Point" mandatory="0" objectRegistryToSelect="udo_Point" filter='Refuel Points only'

$clia $TENANT_ID DataView addParameterDateTime objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Time Last Modified" parameterHandle="startDate" parameterName="Start Date" mandatory="1"

$clia $TENANT_ID DataView addParameterDateTime objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Time Last Modified" parameterHandle="stopDate" parameterName="Stop Date" mandatory="1"

$clia $TENANT_ID DataView addParameterSelect objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" parameterHandle="truck" parameterName="Truck" mandatory="0" objectRegistryToSelect="udo_Truck" filter='active != "NULL"'

$clia $TENANT_ID DataView setFilter objectRegistry="udo_Refuel" type="ItemListDataView" name="Refuel Report (by truck) - Last Modified Time" filter='(authorized = "Unauthorized" OR point_id = @Parameter:refuelPoint) AND time_last_modified >= @Parameter:startDate AND time_last_modified <= @Parameter:stopDate AND driver_id = @Parameter:driver AND truck_id = @Parameter:truck'

$clia $TENANT_ID DataView setOrderBy objectRegistry="udo_Refuel" type="ItemListDataView" name="Refuel Report (by truck) - Last Modified Time" orderByField=refuelOrderNumber_id orderByDirection=ASC

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Fleet No." source="truck_id->fleetnum"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Truck FM ID" source="truck_id->fM"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Refuel Order Number" source="refuelOrderNumber_id"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Refuel Time" source="fillDateTime"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Driver Nickname" source="driver_id->nickname"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Driver FM ID" source="driver_id->fM"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Staff No" source="driver_id->staffNumber"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Location" source="point_id->name"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Unauthorised Location Name" source="unauthorizedLocation"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Odo" source="odo"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Litres" source="litres"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Full / Partial" source="full_or_Partial"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Cost" source="cost"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Fuel Consumption" source="fuelConsumption"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Created by" source="created_by"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Time Created" source="time_created"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Last Edited by" source="last_modified_by"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Time last Edited" source="time_last_modified"

$clia $TENANT_ID DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck) - Last Modified Time" label="Notes" source="notes"

$clia $TENANT_ID Report create name="Refuel Report (by truck) - Last Modified Time" objectRegistry=udo_Refuel dataView="Refuel Report (by truck) - Last Modified Time" dataViewType=ItemListDataView primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Refuel" groupOwnerCrud="Read"
