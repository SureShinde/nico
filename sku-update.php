<?php
$mageFilename = 'app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);

ini_set('display_errors', 'Off');
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

set_time_limit(0);
ini_set('memory_limit','1024M');

    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');


$updates_file="./var/import/import-sku2sku.csv";

$sku_entry=array();
$updates_handle=fopen($updates_file, 'r');
if($updates_handle) {
while($sku_entry=fgetcsv($updates_handle, 1000, ",")) {
$old_sku=$sku_entry[0];
$new_sku=$sku_entry[1];
echo "<br>Updating ".$old_sku." to ".$new_sku." - ";
try {
$get_item = Mage::getModel('catalog/product')->loadByAttribute('sku', $old_sku);
if ($get_item) {
    $product_id = $get_item->getId();
    $writeConnection->update(
            "catalog_product_entity",
            array("sku" => $new_sku),
            "entity_id=$product_id"
    );
echo "successful";
} else {
echo "item not found";
}
} catch (Exception $e) {
echo "Cannot retrieve products from Magento: ".$e->getMessage()."<br>";
return;
}
}
}
fclose($updates_handle);
?>