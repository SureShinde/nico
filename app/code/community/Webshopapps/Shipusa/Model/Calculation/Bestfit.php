<?php
/* UsaShipping
 *
 * Date        1/5/14
 * Author      Karen Baker
 * @category   Webshopapps
 * @package    Webshopapps_UsaShipping
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

class Webshopapps_Shipusa_Model_Calculation_Bestfit {


    public function calculateBoxesForProducts(&$dimArr, &$noDimArr,$bestFitArr) {

        // given a product with an array of possible ship boxes

        // for each product calculate the dimensions
        // then work out how many can fit in the box based on the max quantity and the max weight & volume of product against the box volume
        // we don't check to see whether item can physically fit though

        $tolerance = Mage::getStoreConfig('shipping/shipusa/best_fit_tolerance');

        foreach ($bestFitArr as $bestFitProduct) {
            $productDimensions = $bestFitProduct['width']*$bestFitProduct['length']*$bestFitProduct['height'];
            
            $boxes = array();

            //foreach possible box, calculate max products
            foreach (explode(",", $bestFitProduct['possible_ship_boxes']) as $possibleBox) {

                $boxDetails = Mage::getModel('boxmenu/boxmenu')->load($possibleBox['ship_box_id']);

                //Ensure the shipping box still exists
                if(!$boxDetails->getId()) {
                    if (Mage::helper('shipusa')->isDebug()) {
                        Mage::helper('wsalogger/log')->postDebug('usashipping','Box id no longer exists, skipping id: ',
                            $possibleBox['ship_box_id']);
                    }
                    continue;
                }

                $boxDimensions = $boxDetails['width']*$boxDetails['length']*$boxDetails['height'];
                if (Mage::helper('shipusa')->isDebug()) {
                    Mage::helper('wsalogger/log')->postDebug('usashipping','Calculating for box: ', $possibleBox['ship_box_id']);
                }

                $maxBoxQty = $boxDetails->getMultiplier();
                $maxBoxWeight = $boxDetails->getMaxWeight();

                //below line really doesnt do anything, max products doesnt really limit the amount of items,
                // only true box volume will do that. I will temporarily take care of it in populateDimArr()
                $maxProductsForBoxVolume  = ($boxDimensions-($boxDimensions*.01*
                        $tolerance))/$productDimensions;

                $maxProductsForBoxQty = $maxBoxQty == -1 ? 1000000 : $maxBoxQty;
                $maxProductsForBoxWeight    = $maxBoxWeight == -1 ? 1000000 : $maxBoxWeight/$bestFitProduct['weight'];

                // now take the lowest max products as the one we will use
                $finalMaxQty = min(array($maxProductsForBoxVolume,$maxProductsForBoxWeight,$maxProductsForBoxQty));

                if (Mage::helper('shipusa')->isDebug()) {
                    Mage::helper('wsalogger/log')->postDebug('dimensional','Max Products for Box Volume',
                        $maxProductsForBoxVolume);
                    Mage::helper('wsalogger/log')->postDebug('dimensional','Max Products for Box Qty', $maxProductsForBoxQty);
                    Mage::helper('wsalogger/log')->postDebug('dimensional','Final Max Qty', $finalMaxQty);
                }

                if ($finalMaxQty<=0) {
                    continue;
                }

                $boxes[] = $this->populateDimArr($boxDetails, $finalMaxQty, $productDimensions, $tolerance);
            }
            
            // if can't pack into any box then put in noDim Array
            $bestFitProduct['single_boxes'] = $boxes;

            if (count($boxes)<1) {
                $noDimArr[] = $bestFitProduct;
            } else {
                $dimArr[] = $bestFitProduct;
            }
        }

        return;
    }


    /**
     * Create a box on the fly, using the original box details but modify the max qty that can fit in
     * @param $boxDetails
     * @param $finalMaxQty
     * @return array
     */
    protected function populateDimArr($boxDetails,$finalMaxQty, $boxDimensions, $tolerance)
    {
        $box = array();
        $box['box_id']                      = $boxDetails->getId();
        $box['length']                      = $boxDetails->getLength();
        $box['width']                       = $boxDetails->getWidth();
        $box['height']                      = $boxDetails->getHeight();
        $outerBoxDim = Mage::helper('shipusa')->calculateBoxVolume($box['length'],$box['height'],$box['width']);
        $box['max_shipbox_weight']          = $boxDetails->getMaxWeight() > 0 ? $boxDetails->getMaxWeight() : -1;
        $box['max_shipbox_qty']             = $finalMaxQty;
        $box['perc_qty_per_item']           = $finalMaxQty > 0 ? (1/$finalMaxQty)*100 : -1; //should be percentage of volume taken up?
        $packingWeight                      = $boxDetails->getPackingWeight();
        $box['packing_weight']              = $packingWeight > 0 ? $packingWeight : 0;
        // manually adding in tolerance because algorithm does not look at max_ship_box_qty
        $box['box_volume'] 	                = $outerBoxDim-($outerBoxDim*.01*$tolerance);
        $box['item_volume']                 = $boxDimensions;
        $box['max_box']                     = $boxDetails->getData('max_box');
        $box['max_qty']                     = -1;
        $box['min_qty']                     = 0;

        return $box;
    }
}