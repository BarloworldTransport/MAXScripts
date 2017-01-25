#! /bin/bash

clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Primary Fleet" source="truck_id->primaryFleet_id"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Tripleg Income" source="tripLegIncome"

# 2016-08-02
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Pre-Loading Driver" source="preLoadingDriver_id"