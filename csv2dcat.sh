#!/bin/sh

# This script can be used to convert the csv generated from the
# google doc of the "unofficial" catalog of Sicily datasets:
#
# https://docs.google.com/spreadsheets/d/15cFU9v2x3ACb1IPcVE9Xg3wc5fuay8NAYDXQnoMcOFw/edit#gid=968762763
#
# The csv file must be passed on standard input, and columns must be
# separated by tab. The resulting ontology will be sent on standard output 
# as abbreviated RDF/XML.
#
# @author Cristiano Longo

cat header.owl.part
echo "\t<!-- generation time  "
date
echo "\t-->"
echo '</rdf:RDF>'


