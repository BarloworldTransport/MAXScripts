#! /bin/bash
SOURCE='DateRangeValue.objectInstanceId->udo_FleetTruckLink.id->udo_truck.id->udo_Fleet.name'
clia DataView addField objectRegistry=DateRangeValue type=ItemListDataView name="Trucks in a Fleet" label="Primary Fleet" source="$SOURCE" showInRelatedWidget=0
# clia DataView deleteField objectRegistry=DateRangeValue name='Trucks in a Fleet' type=ItemListDataView source='$SOURCE'