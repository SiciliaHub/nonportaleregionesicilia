<?php
/**
 * Developed at the #ods15 hackaton
 *
 * Convert tha data about the regione Sicilia datasets from a source CSV
 * into a DCAT file. In the resulting DCAT file there are just DCAT elements.
 * Declarations, rdf/XML and ontology elements have to be added later.
 *
 * @author Cristiano Longo
 */
 
$header=file_get_contents('header.owl.part');
echo $header;
 
$handle=fopen('php://stdin', 'r');
if (feof($handle)){
	echo "Empty file!";
}

//ignore first line
fgets($handle);

//used to generate creator URIs
$numCreators=0;

while( $row = fgetcsv($handle,1000,"\t") ){
	$id = $row[0];
	$title = utf8_encode($row[5]);
	$description = utf8_encode($row[6]);
	$home= $row[8];
	$creator = urlencode($row[1]);
	$creator_name=utf8_encode($row[1]);
	echo "\t<dcat:Dataset rdf:about=\"$id\">\n";
	echo "\t\t<dcterms:title>$title</dcterms:title>\n";
	echo "\t\t<rdfs:label>$title</rdfs:label>\n";
	if ($description!=null && strlen($description)>0)
		echo "\t\t<dcterms:description>$description</dcterms:description>\n";
	if ($home!=null && strlen($home)>0)
		echo "\t\t<dcat:landingPage rdf:resource=\"".htmlentities($home)."\" />\n";	

 	echo "\t\t<dc:creator>\n";
	echo "\t\t\t<foaf:Agent rdf:about=\"".$creator."\">\n";
	echo "\t\t\t\t<foaf:name>$creator_name</foaf:name>\n";	
	echo "\t\t\t</foaf:Agent>\n";
	echo "\t\t</dc:creator>\n";	

	echo "\t</dcat:Dataset>\n";
}

echo "</rdf:RDF>\n";
?>