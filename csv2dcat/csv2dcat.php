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

//some constants for columns in the source csv

define('SHEET_URL','https://docs.google.com/spreadsheets/d/15cFU9v2x3ACb1IPcVE9Xg3wc5fuay8NAYDXQnoMcOFw/export?format=tsv&exportFormat=tsv&ndplr=1');
define('THEME_URL_COL', '5');
define('THEME_LABEL_COL', '4');
define('SUBTHEME_URL_COL', '7');
define('SUBTHEME_LABEL_COL', '6');
define('TITLE_COL', '9');
define('DESCRIPTION_COL', '10');
//TODO themes
define('LANDING_PAGE_COL', '12');
define('DOWNLOAD_URL_COL', '13');
define('FORMAT_COL', '14');

//a value use to indicate that the distribution refers to a service
define('SERVICE_MEDIA_TYPE','db');
/**
 * Get the n-th field in the row, if any and if it is not empty.
 * 
 * @param $row a string array
 * @param $n a position in the string array (starting from 0)
 * @return the value of the n-th field in the row array, if exists and
 * it is not empty. Null otherwise.
 */
function getField($row, $n){
	if (count($row)<=$n)
		return null;
	$value=$row[$n];
	if ($value!=null && strlen($value)>0)
		return trim($value);
	return null;
}

/**
 * Check whether the string provided as parameter is a recognized
 * media type.
 * 
 * @param string $mediaType may be null
 */
function isIANAMediaType($mediaType){
	//TODO write me
	return $mediaType!=SERVICE_MEDIA_TYPE;
}

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
	
	$title = $row[TITLE_COL];
	$description = $row[DESCRIPTION_COL];
	$home= $row[LANDING_PAGE_COL];
		
//	$creator = urlencode($row[1]);
//	$creator_name=utf8_encode(htmlentities($row[1],ENT_COMPAT,'utf-8'));
	
	$theme_url=$row[THEME_URL_COL];
	$theme_name=$row[THEME_LABEL_COL];
	$new_theme=!array_key_exists($theme_url, $processed_themes);
		
	$subtheme_url=$row[SUBTHEME_URL_COL];
	$subtheme_name=$row[SUBTHEME_LABEL_COL];
	$new_subtheme=!array_key_exists($subtheme_url, $processed_themes);
	
	echo "\t<dcat:Dataset rdf:about=\"$id\">\n";
	echo "\t\t<dcterms:title>$title</dcterms:title>\n";
	echo "\t\t<rdfs:label>$title</rdfs:label>\n";
	if ($description!=null && strlen($description)>0)
		echo "\t\t<dcterms:description>$description</dcterms:description>\n";
	if ($home!=null && strlen($home)>0)
		echo "\t\t<dcat:landingPage rdf:resource=\"".htmlentities($home)."\" />\n";	

	if ($new_theme){
		echo "\t\t<vocab:mainTheme>\n";
		echo "\t\t\t<skos:Concept rdf:about=\"$theme_url\">\n";
		echo "\t\t\t\t<rdfs:label>$theme_name</rdfs:label>\n";
		echo "\t\t\t\t<skos:prefLabel>$theme_name</skos:prefLabel>\n";
		echo "\t\t\t</skos:Concept>\n";
		echo "\t\t</vocab:mainTheme>\n";
		$processed_themes[$theme_url]=true;
	} else 
		echo "\t\t<vocab:mainTheme rdf:resource=\"$theme_url\" />\n";

	if ($new_subtheme){
		echo "\t\t<vocab:subTheme>\n";
		echo "\t\t\t<skos:Concept rdf:about=\"$subtheme_url\">\n";
		echo "\t\t\t\t<rdfs:label>$subtheme_name</rdfs:label>\n";
		echo "\t\t\t\t<skos:prefLabel>$subtheme_name</skos:prefLabel>\n";
		echo "\t\t\t\t<skos:broader rdf:resource=\"$theme_url\" />\n";
		echo "\t\t\t</skos:Concept>\n";
		echo "\t\t</vocab:subTheme>\n";
		$processed_themes[$subtheme_url]=true;
	} else
		echo "\t\t<vocab:subTheme rdf:resource=\"$subtheme_url\" />\n";
	
	$downloadURL=getField($row, DOWNLOAD_URL_COL);
	$mediaType=getField($row, FORMAT_COL);
	$isService=$mediaType!=null && 'db'==$mediaType;
	
	if ($downloadURL!=null){
		$downloadURLRefined=htmlentities($downloadURL); 
		echo "\t\t<dcat:distribution>\n";
		echo "\t\t\t<dcat:Distribution rdf:about=\"".$id."dist\">\n";
		
		//discriminate download files from services
		$downloadURLProperty=$isService ? "dcat:accessURL" :  "dcat:downloadURL";
		echo "\t\t\t\t<$downloadURLProperty rdf:resource=\"$downloadURLRefined\" />\n";
		
		//check if it is a valid media type
		if (isIANAMediaType($mediaType))
			echo "\t\t\t\t<dcat:mediaType>$mediaType</dcat:mediaType>\n";
			
		echo "\t\t\t</dcat:Distribution>\n";
		echo "\t\t</dcat:distribution>\n";
	}
	
	
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