#! /bin/bash

# Add fields
clia DataView addField objectRegistry=udo_Truck type=ItemListDataView name="Trucks in Province" label="Location Label" source="udo_Position.truck_id->autoLocationLabel"

# Set grouping
clia Report setGroupBy name='Trucks in Province' newGroupBy=id