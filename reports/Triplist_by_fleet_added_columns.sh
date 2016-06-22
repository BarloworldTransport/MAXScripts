#! /bin/bash

clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Primary Fleet" source="cargo_id->litresOffloaded"
clia DataView addField objectRegistry=udo_TripLeg type=ItemListDataView name="Triplist by Fleet" label="Tripleg Income" source="cargo_id->litresOffloaded"