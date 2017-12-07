#! /bin/bash

clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Primary Fleet" source="truck_id->primaryFleet_id"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Tripleg Income" source="tripLegIncome"

# 2016-08-02
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Pre-Loading Driver" source="preLoadingDriver_id"

# 2017-02-01
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Time Created" source="time_created"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Created By" source="created_by"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Last Update Time" source="time_last_modified"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Last Updated By" source="last_modified_by"

# 2017-11-30
clia DataView deleteField objectRegistry=udo_TripLeg name="Triplist by Fleet" type=ItemListDataView source="cargo_id->litresInvoiceable"
clia DataView deleteField objectRegistry=udo_TripLeg name="Triplist by Fleet" type=ItemListDataView source="cargo_id->litresLoaded"
clia DataView deleteField objectRegistry=udo_TripLeg name="Triplist by Fleet" type=ItemListDataView source="cargo_id->litresOffloaded"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Depot Odo" source="cargo_id->udo_odo.objectInstanceId->odo"
