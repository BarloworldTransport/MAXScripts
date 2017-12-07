#! /bin/bash

# 2016-08-19 ticket number: 10635 
clia DataView addField objectRegistry=udo_Cargo type=ItemListDataView name="Trips to Invoice" label="Customer Syspro Code" source="customer_id->sYSProCode" showInRelatedWidget=0

# 2017-06-02 - W Krige request
clia DataView addField objectRegistry=udo_Cargo type=ItemListDataView name="Trips to Invoice" label="City From" source="locationFrom_id->parent_id" showInRelatedWidget=0
clia DataView addField objectRegistry=udo_Cargo type=ItemListDataView name="Trips to Invoice" label="City To" source="locationTo_id->parent_id" showInRelatedWidget=0