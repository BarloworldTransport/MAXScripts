#! /bin/bash

clia DataView addField objectRegistry=udo_TripLegCargo type=ItemListDataView name="Trips by Truck" label="Loading Region" source="tripLeg_id->locationFromPoint_id->parent_id->parent_id->name" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLegCargo type=ItemListDataView name="Trips by Truck" label="Offloading Region" source="tripLeg_id->locationToPoint_id->parent_id->parent_id->name" showInRelatedWidget=0

# 2016-08-19
clia DataView addField objectRegistry=udo_TripLegCargo type=ItemListDataView name="Trips by Truck" label="Product Category" source="cargo_id->productCategory_id->name" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLegCargo type=ItemListDataView name="Trips by Truck" label="Product Type" source="cargo_id->productType_id->name" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_TripLegCargo type=ItemListDataView name="Trips by Truck" label="Product Detail" source="cargo_id->productDetail_id->name" showInRelatedWidget=0