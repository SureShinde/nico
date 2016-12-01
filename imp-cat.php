<?php

error_reporting(0);
define('MAGENTO', realpath(dirname(__FILE__)."/.."));
 //require_once MAGENTO . '/app/Mage.php';
 require_once(__DIR__ .'/app/Mage.php');
 umask(0);
 Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
 $write = Mage::getSingleton('core/resource')->getConnection('core_write');
 $count = 0;
 $headers = array();
 
 //live file 
 //$file = fopen(__DIR__ . '/var/import/category/import-update-categories-revision3-live.csv', 'r');
 //demo new site file
 $file = fopen(__DIR__ . '/var/import/category/final-live.csv', 'r');
    
 $_category = Mage::getSingleton('catalog/category');
 
 while (($line = fgetcsv($file)) !== FALSE) {
     $count++;
 
 // First header row
 if ($count == 1) {
 foreach ($line as $id=>$col)
 $headers[$col] = $id;
 
 continue;
 }
 //print_r($line[$headers['Name']]);exit;
$category = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('name',$line[$headers['Name']]);
$cat_det=$category->getData();
$category_id=$cat_det[0][entity_id];//exit;
 
 
 $_category->load($line[$headers['ID']]);
 
 //$_category->setMetaTitle($line[$headers['meta_title']]);
 //$_category->setMetaDescription($line[$headers['meta_description']]);
 //$_category->setDescription($line[$headers['additional_description']]);
 //$_category->setUrlKey($line[$headers['url_key']]);
 //echo $category_id;
echo $line[$headers['ID']]; //exit;
 
$_category->setName($line[$headers['Name']]);
$_category->setPosition($line[$headers['Position']]);
$_category->setIsActive($line[$headers['Is Active']]);
$_category->setUrlKey($line[$headers['Url Key']]);
$_category->setDescription($line[$headers['Description']]);
$_category->setImage($line[$headers['Image']]);
$_category->setThumbnail($line[$headers['Thumbnail Image']]);
$_category->setMetaTitle($line[$headers['Page Title']]);
$_category->setMetaKeywords($line[$headers['Meta Keywords']]);
$_category->setMetaDescription($line[$headers['Meta Description']]);
$_category->setLandingPage($line[$headers['landing_page']]);
$_category->setIncludeInMenu($line[$headers['Include In Menu']]);
$_category->setDisplayMode($line[$headers['Display Mode']]);
$_category->setLandingPage($line[$headers['CMS Block']]);
$_category->setIsAnchor($line[$headers['Is Anchor']]);
$_category->setAvailabeSortBy($line[$headers['Availabe Sort By']]);
$_category->setDefaultSortBy($line[$headers['Default Sort By']]);
$_category->setPageLayout($line[$headers['Page Layout']]);
$_category->setCustomLayoutUpdate($line[$headers['Custom Layout Update']]);

$_category->setSwCatBlockType($line[$headers['sw_cat_block_type']]); //default,wide,staticwidth,narrow
$_category->setSwCatStaticWidth($line[$headers['sw_cat_static_width']]);
$_category->setSwCatBlockColumns($line[$headers['sw_cat_block_columns']]);
$_category->setSwCatBlockTop($line[$headers['sw_cat_block_top']]);
$_category->setSwCatLeftBlockWidth($line[$headers['sw_cat_left_block_width']]);
$_category->setSwCatBlockLeft($line[$headers['sw_cat_block_left']]);
$_category->setSwCatRightBlockWidth($line[$headers['sw_cat_right_block_width']]);
$_category->setSwCatBlockRight($line[$headers['sw_cat_block_right']]);
$_category->setSwCatBlockBottom($line[$headers['sw_cat_block_bottom']]);
$_category->setSwCatLabel($line[$headers['sw_cat_label']]);


 //$sql = "INSERT INTO catalog_category_entity_varchar (entity_type_id, attribute_id, store_id, entity_id, value) VALUES (3, 45, 0, ".$category_id.", '".$line[$headers['Image']]."') ON DUPLICATE KEY UPDATE value = '".$line[$headers['Image']]."';";
 //New site sql SELECT * FROM `catalog_category_entity_varchar` WHERE `value` LIKE '%.png%' //
  //$sql = "INSERT INTO catalog_category_entity_varchar (entity_type_id, attribute_id, store_id, entity_id, value) VALUES (9, 967, 0, ".$category_id.", '".$line[$headers['Thumbnail Image']]."') ON DUPLICATE KEY UPDATE value = '".$line[$headers['Thumbnail Image']]."';";
//new site sql  

//live site sql SELECT * FROM `catalog_category_entity_varchar` WHERE `value` LIKE '%.png%' //
  $sql = "INSERT INTO catalog_category_entity_varchar (entity_type_id, attribute_id, store_id, entity_id, value) VALUES (9, 113, 0, ".$category_id.", '".$line[$headers['Thumbnail Image']]."') ON DUPLICATE KEY UPDATE value = '".$line[$headers['Thumbnail Image']]."';";
   //exit;
 //$write->query($sql);
//live site sql  


 
  $src = __DIR__ .'/media/import/'.$line[$headers['Thumbnail Image']];
  $dest = __DIR__ .'/media/catalog/category/'.$line[$headers['Thumbnail Image']];
 
copy($src, $dest);
 

 $_category->save();
 
 echo "Completed ".$line[$headers['Name']]."<br />";

 } 