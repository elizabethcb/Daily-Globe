<?php 
include_once('wp-config.php');
echo '<?xml version="1.0" encoding="UTF-8"?>
';

$type = $_GET["type"];
$id = $_GET["id"];


//connection to the database
$dbhandle = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
  or die("Unable to connect to MySQL");
$selected = mysql_select_db(DB_NAME,$dbhandle)
  or die("Could not select examples");

function getCities() {
	$table = 'wp_blogs';
	$query = 'SELECT blog_name displayTitle, blog_id id
FROM '. $table .'
WHERE blog_type = "city"
ORDER BY blog_name ASC';
	$result = mysql_query($query); ?>
<cities>
   <city>
      <displayTitle>U.S. News</displayTitle>
      <id>64</id>
   </city>
<?php
	while ($row = mysql_fetch_array($result)) { ?>
   <city>
      <displayTitle><?php echo $row{'displayTitle'}; ?></displayTitle>
      <id><?php echo $row{'id'}; ?></id>
   </city>
<?php } ?>
</cities>
<?php
}

function getTopics() {
	$table = 'wp_blogs';
	$query = 'SELECT blog_name displayTitle, blog_id id
FROM '. $table .'
WHERE blog_type = "topic"
ORDER BY blog_name ASC';
	$result = mysql_query($query); ?>
<topics>
<?php
	while ($row = mysql_fetch_array($result)) { ?>
   <topic>
      <displayTitle><?php echo $row{'displayTitle'}; ?></displayTitle>
      <id><?php echo $row{'id'}; ?></id>
   </topic>
<?php } ?>
</topics>
<?php
}

function getCategoriesForID($id) {
//subcategories
//SELECT A1.name displayTitle, A1.slug link, A2.parent parentID, A3.name parentTitle FROM wp_3_terms A1, wp_3_term_taxonomy A2, wp_3_terms A3 WHERE A1.term_id = A2.term_id AND A2.taxonomy = "category" AND A2.parent != 0 AND A2.parent = A3.term_id ORDER BY parentTitle ASC

//categories
//SELECT A1.name displayTitle, A1.slug link, A2.term_id ID, A3.image_src imageURL FROM wp_3_terms A1, wp_3_term_taxonomy A2 LEFT JOIN category_images A3 ON (A2.term_id = A3.term_id) WHERE A1.term_id = A2.term_id AND A2.taxonomy = "category" AND A2.parent = 0 GROUP BY displayTitle ORDER BY displayTitle ASC
	if($location = checkValid($id)){
	$table1 = 'wp_'. $id . '_terms';
	$table2 = 'wp_'. $id . '_term_taxonomy';
	$table3 = 'category_images';
	$query = 'SELECT A1.name displayTitle, A1.slug link, A2.term_id ID, A3.image_src imageURL, A2.term_id ID 
FROM '.$table1.' A1, '.$table2.' A2 
LEFT JOIN '.$table3.' A3 ON (A2.term_id = A3.term_id) 
WHERE A1.term_id = A2.term_id AND A2.taxonomy = "category" AND A2.parent = 0 AND A1.name != "Uncategorized" 
GROUP BY displayTitle 
ORDER BY displayTitle ASC';
	$categories = mysql_query($query);
	$query = 'SELECT A1.name displayTitle, A1.slug link, A2.parent parentID, A3.name parentTitle, A4.image_src imageURL, A2.term_id ID 
FROM '.$table1.' A1 LEFT JOIN '.$table3.' A4 ON (A1.term_id = A4.term_id), '.$table2.' A2, '.$table1.' A3  
WHERE A1.term_id = A2.term_id AND A2.taxonomy = "category" AND A2.parent != 0 AND A2.parent = A3.term_id AND A1.name != "Uncategorized" 
GROUP BY displayTitle 
ORDER BY parentTitle ASC, displayTitle ASC';
	$subcategories = mysql_query($query);?>
	
<categories>
   <category>
      <displayTitle>Categories</displayTitle>
      <catLink>0</catLink>
      <imageURL></imageURL>
      <id></id>
   </category>
   <category>
      <displayTitle>Top Stories</displayTitle>
      <catLink>http://<?php echo $location . '/feed/'; ?></catLink>
      <imageURL>http://farm3.static.flickr.com/2203/2208986720_603c4f6433.jpg</imageURL>
      <id>Top Stories</id>
   </category>
<?php
	$subcategory = mysql_fetch_array($subcategories);
	while ($category = mysql_fetch_array($categories)) { ?>
   <category>
      <displayTitle><?php echo $category{'displayTitle'}; ?></displayTitle>
      <catLink>http://<?php echo $location .'/category/'. $category{'link'} . '/feed/'; ?></catLink>
      <imageURL><?php echo $category{'imageURL'}; ?></imageURL>
      <id><?php echo $category{'ID'}; ?></id>
   </category>
<?php
		while ($subcategory{'parentID'} == $category{'ID'}) { ?>
   <category>
      <displayTitle><?php echo '    '. $subcategory{'displayTitle'}; ?></displayTitle>
      <catLink>http://<?php echo $location .'/category/'. $category{'link'} . '/'. $subcategory{'link'} .'/feed/'; ?></catLink>
      <imageURL><?php echo $subcategory{'imageURL'}; ?></imageURL>
      <id><?php echo $category{'ID'}; ?></id>
   </category>
<?php
   		$subcategory = mysql_fetch_array($subcategories);
   		}
	} ?>
</categories>
<?php
} elseif($id == NULL){?>
<error>no id specified</error>
<?php
} else{?>
<error>invalid id specified</error>
<?php
}
}

function checkValid($id) {
	global $location;
	$table = 'wp_blogs';
	$query = 'SELECT *
FROM '. $table .'
WHERE blog_id = '. $id .' 
ORDER BY blog_name ASC';
	$result = mysql_query($query); 
	if($row = mysql_fetch_array($result)){
		return $row{'domain'};
	} else {
		return false;
	}
}

if($type == "cities") {
	getCities();
} elseif($type == "topics") {
	getTopics();
} elseif($type == 'categories'){
	getCategoriesForID($id);
} else{
echo "<error>incorrect type specified</error>";
}
?>