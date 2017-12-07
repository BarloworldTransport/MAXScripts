#! /bin/bash
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Income for Export no calc" label="Business Unit" source="businessUnit_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Income for Export no calc" label="Primary Fleet" source="truck_id->primaryFleet_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Income for Export no calc" label="Rate Type" source="udo_TripLegCargo.tripLeg_id->cargo_id->rate_id->rateType_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Income for Export no calc" label="Rate" source="udo_TripLegCargo.tripLeg_id->cargo_id->rate_id" showInRelatedWidget=0
# 2017-09-05
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Income for Export no calc" label="Tons" source="udo_TripLegCargo.tripLeg_id->cargo_id->tonsActual" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Income for Export no calc" label="Pallets" source="udo_TripLegCargo.tripLeg_id->cargo_id->palletsInvoiceable" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Income for Export no calc" label="Pockets" source="udo_TripLegCargo.tripLeg_id->cargo_id->pocketsInvoiceable" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Income for Export no calc" label="Litres" source="udo_TripLegCargo.tripLeg_id->cargo_id->litresInvoiceable" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Income for Export no calc" label="Trip Number" source="udo_TripLegCargo.tripLeg_id->cargo_id->tripNumber" showInRelatedWidget=0
