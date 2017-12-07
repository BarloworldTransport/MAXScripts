#! /bin/bash

# Added 2017-07-14 
clia DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by fleet)" label="Truck Registration No." source="truck_id->licensePlate" showInRelatedWidget=0

# Added 2017-09-11
clia DataView deleteField objectRegistry=udo_Refuel name="Refuel Report (by truck)" type=ItemListDataView source="fuelConsumption"
clia DataView addField objectRegistry=udo_Refuel type=ItemListDataView name="Refuel Report (by truck)" label="Truck Registration No." source="truck_id->licensePlate" showInRelatedWidget=0