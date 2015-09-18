<?php
/**
 * Developed at the #ods15 hackaton
 *
 * Convert tha data about the regione Sicilia datasets from a source CSV
 * into a DCAT file. In the resulting DCAT file there are just DCAT elements.
 * Declarations, rdf/XML and ontology elements have to be added later.
 *
 * This software is release under the CC-BY 4.0 license (https://creativecommons.org/licenses/by/4.0/)
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

// $xmlDoc=new DOMDocument();
// $xmlDoc->encoding="utf-8";
// $xmlDoc->doctype="rdf:RDF";
// echo $xmlDoc->saveXML();

$processed_themes=array();
while( $row = fgetcsv($handle,2000,"\t") ){
	$id = $row[0];
	if (count($row)<11) 
		echo "<!-- invalid number of columns in row $id -->\n";
	
//	$title = utf8_encode(htmlentities($row[7],ENT_COMPAT,'utf-8'));
	$title = utf8_encode($row[7]);
	$description = utf8_encode($row[8]);
//	$home= utf8_encode(htmlentities($row[10],ENT_COMPAT,'utf-8'));
	$home= $row[10];
	$creator = urlencode($row[1]);
	$creator_name=utf8_encode(htmlentities($row[1],ENT_COMPAT,'utf-8'));
	
	$theme_url=$row[4];
	$theme_name=$row[3];
	$new_theme=!array_key_exists($theme_url, $processed_themes);
		
	$subtheme_url=$row[6];
	$subtheme_name=$row[5];
	$new_subtheme=!array_key_exists($subtheme_url, $processed_themes);
	
	echo "\t<dcat:Dataset rdf:about=\"$id\">\n";
	echo "\t\t<dcterms:title>$title</dcterms:title>\n";
	echo "\t\t<rdfs:label>$title</rdfs:label>\n";
	if ($description!=null && strlen($description)>0)
		echo "\t\t<dcterms:description>$description</dcterms:description>\n";
	if ($home!=null && strlen($home)>0)
		echo "\t\t<dcat:landingPage rdf:resource=\"".htmlentities($home)."\" />\n";	

	if ($new_theme){
		echo "\t\t<dcat:theme>\n";
		echo "\t\t\t<skos:Concept rdf:about=\"$theme_url\">\n";
		echo "\t\t\t\t<rdfs:label>$theme_name</rdfs:label>\n";
		echo "\t\t\t\t<skos:prefLabel>$theme_name</skos:prefLabel>\n";
		echo "\t\t\t</skos:Concept>\n";
		echo "\t\t</dcat:theme>\n";
		$processed_themes[$theme_url]=true;
	} else 
		echo "\t\t<dcat:theme rdf:resource=\"$theme_url\" />\n";

	if ($new_subtheme){
		echo "\t\t<dcat:theme>\n";
		echo "\t\t\t<skos:Concept rdf:about=\"$subtheme_url\">\n";
		echo "\t\t\t\t<rdfs:label>$subtheme_name</rdfs:label>\n";
		echo "\t\t\t\t<skos:prefLabel>$subtheme_name</skos:prefLabel>\n";
		echo "\t\t\t\t<skos:broader rdf:resource=\"$theme_url\" />\n";
		echo "\t\t\t</skos:Concept>\n";
		echo "\t\t</dcat:theme>\n";
		$processed_themes[$subtheme_url]=true;
	} else
		echo "\t\t<dcat:theme rdf:resource=\"$subtheme_url\" />\n";
	
/* 	echo "\t\t<dc:creator>\n";
	echo "\t\t\t<foaf:Agent rdf:about=\"".$creator."\">\n";
	echo "\t\t\t\t<foaf:name>$creator_name</foaf:name>\n";	
	echo "\t\t\t</foaf:Agent>\n";
	echo "\t\t</dc:creator>\n";	
*/
	echo "\t</dcat:Dataset>\n";
}

echo "</rdf:RDF>\n";
?>