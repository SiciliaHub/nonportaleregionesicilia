<?php
/**
 * Developed at the #ods15 hackaton
 *
 * Convert tha data about the regione Sicilia datasets from a source CSV
 * into a DCAT file. In the resulting DCAT file there are just DCAT elements.
 * Declarations, rdf/XML and ONoltogy elements have to be added later.
 *
 * @author Cristiano Longo
 */
$handle=fopen('php://stdin', 'r');
if (feof($handle)){
	echo "Empty file!";
}

//ignore first line
fgets($handle);

while( $row = fgetcsv($handle,1000,"\t") ){
	$id = $row[0];
	$title = $row[5];
	$description = $row[6];
	$home= $row[8];
	$creator = htmlentities($row[1]);
	$creator_name=$row[1];
	echo "<dcat:Dataset rdf:about=\"$id\">\n";
	echo "\t<dcterms:title>$title</dcterms:title>\n";
	echo "\t<rdfs:label>$title</rdfs:label>\n";
	if ($description!=null && strlen($description)>0)
		echo "\t<dcterms:description>$description</dcterms:description>\n";
	if ($home!=null && strlen($home)>0)
		echo "\t<dcat:landingPage rdf:resource=\"".$home."\" />\n";	

 	echo "\t<dc:creator>\n";
	echo "\t\t<foaf:Agent rdf:about=\"".$creator."\">\n";
	echo "\t\t<foaf:name>$creator_name</foaf:name>\n";	
	echo "\t</dc:creator>\n";	

	echo "</dcat:Dataset>\n";
}
?>