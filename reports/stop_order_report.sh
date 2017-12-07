#! /bin/bash

clia DataView createItemList objectRegistry="udo_StopOrder" name="Stop Order Report" internalCreator="Report" primaryOwner="Admin" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Reports" groupOwnerCrud="Read"
clia DataView setShowOptions objectRegistry="udo_StopOrder" type="ItemListDataView" name="Stop Order Report" detailAction="Open" showActions=0 showExport=1 showFooterBar=1
clia DataView setIcon objectRegistry="udo_StopOrder" type="ItemListDataView" name="Stop Order Report" icon="icon-reports.gif"
clia DataView setPageSize objectRegistry="udo_StopOrder" type="ItemListDataView" name="Stop Order Report" pageSize="500"
clia DataView setNoRecordsMessage objectRegistry="udo_StopOrder" type="ItemListDataView" name="Stop Order Report" message="Unfortunately no records were returned."
clia DataView addParameterDateTime objectRegistry=udo_StopOrder type=ItemListDataView name="Stop Order Report" parameterHandle="beginDate" parameterName="Start Date" mandatory="1"
clia DataView addParameterDateTime objectRegistry=udo_StopOrder type=ItemListDataView name="Stop Order Report" parameterHandle="stopDate" parameterName="Stop Date" mandatory="1"
clia DataView addParameterSelect objectRegistry=udo_StopOrder type=ItemListDataView name="Stop Order Report" parameterHandle="driver" parameterName="Driver" mandatory="0" objectRegistryToSelect="udo_Driver" filter='active = "true"'
clia DataView setFilter objectRegistry="udo_StopOrder" type="ItemListDataView" name="Stop Order Report" filter='driver_id = @Parameter:driver AND time_created>=@Parameter:beginDate AND time_created<@Parameter:stopDate AND _status = null'
clia DataView setOrderBy objectRegistry="udo_StopOrder" type="ItemListDataView" name="Stop Order Report" orderByField=driver_id orderByDirection=ASC
clia DataView setOrderBy objectRegistry="udo_StopOrder" type="ItemListDataView" name="Stop Order Report" orderByField=id orderByDirection=ASC
clia DataView addField objectRegistry=udo_StopOrder type=ItemListDataView name="Stop Order Report" label="Driver" source="driver_id"
clia DataView addField objectRegistry=udo_StopOrder type=ItemListDataView name="Stop Order Report" label="Reason" source="reason"
clia DataView addField objectRegistry=udo_StopOrder type=ItemListDataView name="Stop Order Report" label="Time Created" source="time_created"
clia DataView addField objectRegistry=udo_StopOrder type=ItemListDataView name="Stop Order Report" label="Created By" source="created_by"
clia Report create name="Stop Order Report" objectRegistry=udo_StopOrder dataView="Stop Order Report" dataViewType=ItemListDataView primaryOwner="Justin Ward" primaryOwnerCrud="Create, Read, Update, Delete" groupOwner="Manline" groupOwnerCrud="Read"




# 2017-02-14
clia DataView addField objectRegistry=udo_StopOrder type=ItemListDataView name="Stop Order Report" label="Order Number" source="ID" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_StopOrder type=ItemListDataView name="Stop Order Report" label="Comment" source="ID->ObjectNote.objectInstanceId->text" showInRelatedWidget=0