#!/bin/sh

curl -vs "https://docs.google.com/spreadsheets/d/15cFU9v2x3ACb1IPcVE9Xg3wc5fuay8NAYDXQnoMcOFw/export?format=tsv&exportFormat=tsv&ndplr=1" | \
php csv2dcat.php >nonportale.owl
curl -X PUT -H "Content-Type: application/rdf+xml" -u 'cristianolongo'  --data-binary @nonportale.owl http://dydra.com/cristianolongo/dataset-regione-sicilia-ods15/service

