<?php

/**
 * Sunovisio Extensions
 * http://ecommerce.sunovisio.com
 *
 * @extension   Affiliate Network Connector
 * @type        Utility
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Sunovisio
 * @package     Sunovisio_AffiliateNetworkConnector
 * @copyright   Copyright (c) 2012 Sunovisio (http://sunovisio.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Sunovisio_AffiliateNetworkConnector_Helper_Data extends Mage_Core_Helper_Abstract {
	
	//------------------------------------
	// General Settings
	//------------------------------------	
	//Extension Enabled
    public function isEnabled() {
        return Mage::getStoreConfig('affiliatenetworkconnector/general/enabled', Mage::app()->getStore());
    }
	
	// Default Network
    public function getDefaultNetwork () {
        return Mage::getStoreConfig('affiliatenetworkconnector/general/default_network', Mage::app()->getStore());
    }
	
	// Network Priority
    public function getNetworkPriority () {
        return Mage::getStoreConfig('affiliatenetworkconnector/general/network_priority', Mage::app()->getStore());
    }	
	
	// Network Switch To Indirect For Affiliate Override
	public function isNetworkIndirectOverride() {
        return Mage::getStoreConfig('affiliatenetworkconnector/general/network_indirect_override', Mage::app()->getStore());
    }	
		
	// Direct Expiration Days
    public function getDirectExpirationDays () {
        return Mage::getStoreConfig('affiliatenetworkconnector/general/direct_expiration_days', Mage::app()->getStore());
    }		
	
	
	//------------------------------------
	// Debug Mode
	//------------------------------------
	public function isDebugTracking () {
        return Mage::getStoreConfig('affiliatenetworkconnector/debug/debug_tracking', Mage::app()->getStore());
    }

    public function isDebugPixel () {
        return Mage::getStoreConfig('affiliatenetworkconnector/debug/debug_pixel', Mage::app()->getStore());
    }
	
	//------------------------------------
	// Cake
	//------------------------------------
    public function isCakeEnabled() {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_enabled', Mage::app()->getStore());
    }

    public function getCakeMerchantId() {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_merchant_id', Mage::app()->getStore());
    }

    public function getCakePriority () {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_priority', Mage::app()->getStore());
    }	
		
	public function getCakeAffiliateExpirationDays() {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_affiliate_expiration_days', Mage::app()->getStore());
    }
			
    public function isCakeShowZeroPixel() {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_show_zero_pixel', Mage::app()->getStore());
    }
	
	// Residual Commission    
	public function isCakeResidualEnabled() {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_residual_enabled', Mage::app()->getStore());
    }	
	
	public function getCakeResidualExpirationDays01() {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_residual_expiration_days_01', Mage::app()->getStore());
    }	
		
	public function getCakeResidualExpirationDays02() {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_residual_expiration_days_02', Mage::app()->getStore());
    }
	
	// Indirect Commission
    public function isCakeIndirectEnabled() {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_indirect_enabled', Mage::app()->getStore());
    }	
		
	public function isCakeOrganicIndirectOverride() {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_organic_indirect_override', Mage::app()->getStore());
    }	
	
    public function isCakeAffiliateIndirectOverride() {
        return Mage::getStoreConfig('affiliatenetworkconnector/cake/cake_affiliate_indirect_override', Mage::app()->getStore());
    }	
	
	
	//------------------------------------
	// Share A Sale
	//------------------------------------
    public function isShareASaleEnabled() {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_enabled', Mage::app()->getStore());
    }

    public function getShareASaleMerchantId() {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_merchant_id', Mage::app()->getStore());
    }

    public function getShareASalePriority () {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_priority', Mage::app()->getStore());
    }	
		
	public function getShareASaleAffiliateExpirationDays() {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_affiliate_expiration_days', Mage::app()->getStore());
    }
			
    public function isShareASaleShowZeroPixel() {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_show_zero_pixel', Mage::app()->getStore());
    }
	
	
	// Residual Commission    
	public function isShareASaleResidualEnabled() {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_residual_enabled', Mage::app()->getStore());
    }	
	
	public function getShareASaleResidualExpirationDays01() {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_residual_expiration_days_01', Mage::app()->getStore());
    }	
		
	public function getShareASaleResidualExpirationDays02() {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_residual_expiration_days_02', Mage::app()->getStore());
    }
	
	// Indirect Commission
    public function isShareASaleIndirectEnabled() {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_indirect_enabled', Mage::app()->getStore());
    }	
	
    public function isShareASaleOrganicIndirectOverride() {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_organic_indirect_override', Mage::app()->getStore());
    }	
	
    public function isShareASaleAffiliateIndirectOverride() {
        return Mage::getStoreConfig('affiliatenetworkconnector/shareasale/shareasale_affiliate_indirect_override', Mage::app()->getStore());
    }		
	
	
	//------------------------------------
	// Ebay Enterprise
	//------------------------------------
    public function isEbayEnterpriseEnabled() {
        return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_enabled', Mage::app()->getStore());
    }

    public function getEbayEnterpriseMerchantId() {
        return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_merchant_id', Mage::app()->getStore());
    }
	    
	public function getEbayEnterprisePriority () {
        return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_priority', Mage::app()->getStore());
    }	
	
	public function getEbayEnterpriseAffiliateExpirationDays() {
        return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_affiliate_expiration_days', Mage::app()->getStore());
    }
		
    public function isEbayEnterpriseShowZeroPixel() {
        return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_show_zero_pixel', Mage::app()->getStore());
    }
	

	// Residual Commission    
	public function isEbayEnterpriseResidualEnabled() {
        return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_residual_enabled', Mage::app()->getStore());
    }	
	
	public function getEbayEnterpriseResidualExpirationDays01() {
        return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_residual_expiration_days_01', Mage::app()->getStore());
    }	
		
	public function getEbayEnterpriseResidualExpirationDays02() {
        return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_residual_expiration_days_02', Mage::app()->getStore());
    }
	
	// Indirect Commission
    public function isEbayEnterpriseIndirectEnabled() {
       return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_indirect_enabled', Mage::app()->getStore());
    }	
   
    public function isEbayEnterpriseOrganicIndirectOverride() {
        return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_organic_indirect_override', Mage::app()->getStore());
    }	
	
	public function isEbayEnterpriseAffiliateIndirectOverride() {
        return Mage::getStoreConfig('affiliatenetworkconnector/ebayenterprise/ebayenterprise_affiliate_indirect_override', Mage::app()->getStore());
    }	
		
	
	//------------------------------------
	// Sale Tracking
	//------------------------------------	
	public function trackSale() {
		
		//Set Default Pixel
		$pixel = "";
		
		if($this->isEnabled()){
		
			//--------------------------------
			//General Default Values
			//--------------------------------
			$curdatetime = Mage::getModel('core/date')->date('Y-m-d H:i:s');
		
			
			//--------------------------------
			//Load Session Information
			//--------------------------------
			$magsession = Mage::getSingleton('core/session');	
			$magcookie = Mage::getSingleton('core/cookie');
			$sess_affstatus = $magsession->getAffstatus();
			$sess_affnetwork = $magsession->getAffnetwork();
			$sess_affid = $magsession->getAffid();
			$sess_affreqid = $magsession->getAffreqid();
			$sess_afflog = $magsession->getAfflog();

			//--------------------------------
			//Load Customer Information
			//--------------------------------
			$customerid = Mage::getSingleton('customer/session')->getCustomerId();
			$customer = Mage::getModel('customer/customer')->load($customerid);
			
			//Order Count
			$ordercollection = Mage::getModel('sales/order')->getCollection()->addFilter('customer_id', $customerid)->setOrder('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC);
			$count = $ordercollection->count();
			
			// Last Order Information
			$ordercollection = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSelect('increment_id')
                ->addAttributeToSelect('created_at')
                ->addAttributeToSelect('customer_id')
                ->addAttributeToSelect('store_id')
                ->addFilter('customer_id', $customerid)
                ->addAttributeToSort('created_at', 'desc')
                ->setPageSize('1') 
                ->setCurPage(2)       
                ->load(0);
            
			$lastorder = $ordercollection->getFirstItem();
			$lastorderdate = $lastorder->getData('created_at');
			
			// Customer Affiliate Values
			if ($customerid) {
				$affstatus = $customer->getAffstatus();				
				$affnetwork = $customer->getAffnetwork();
				$affid = $customer->getAffid();
				$affreqid = $customer->getAffreqid();
				$affdate = $customer->getAffdate();
				$afflog = $customer->getAfflog();
       		}			
			
			//--------------------------------
			//Load Order Information
			//--------------------------------
			$orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
			$order = Mage::getModel('sales/order')->load($orderId);
			$realorderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
			$_totalData = $order->getData();
			$_sub = $_totalData['subtotal'];
			$_discount = $_totalData['discount_amount'];
			$_tax = $_totalData['tax_amount'];
			$_shipping = $_totalData['shipping_amount'];
			$_total = $_sub + $_discount;	
			$_grandtotal = $_sub + $_discount + $_tax + $_shipping;
			
			
			//TroubleShooting
			//echo "Order Count:".$count."<br/>";
			//echo "Last Order:".$realorderId."<br/>";
			//echo "Second to Last Order:".$ordercollection->getFirstItem()->getData('increment_id')."<br/>";
			//echo "Second to Last Order Date:".$lastorderdate."<br/>";

			
			//--------------------------------------------------
			//Check If Affiliate Expired and Should Be Replaced
			//--------------------------------------------------
			$newaffiliate = false;
			
			if ($count==1){
				$newcustomer = 1;
				
				if($sess_affnetwork == 'Cake' && $this->isCakeEnabled()==true){
					$newaffiliate = true;
				}
				
				elseif($sess_affnetwork == 'Share-A-Sale' && $this->isShareASaleEnabled()==true){
					$newaffiliate = true;
				}
				
				elseif($sess_affnetwork == 'Ebay Enterprise' && $this->isEbayEnterpriseEnabled()==true){
					$newaffiliate = true;
				}

			}
			
			elseif ($count>1){
				$newcustomer = 0;
				// Get Days Since Last Order
				$lastorderdays = floor((strtotime($curdatetime) - strtotime($lastorderdate)) / (60 * 60 * 24));
				
				// Get Affiliate Expiration
				if ($affnetwork == 'Cake' && $this->isCakeEnabled()){
					$affiliateexpirationdays = $this->getCakeAffiliateExpirationDays();
				}				
				
				elseif ($affnetwork == 'Share-A-Sale' && $this->isShareASaleEnabled()){
					$affiliateexpirationdays = $this->getShareASaleAffiliateExpirationDays();
				}
				
				elseif ($affnetwork == 'Ebay Enterprise' && $this->isEbayEnterpriseEnabled()){
					$affiliateexpirationdays = $this->getEbayEnterpriseAffiliateExpirationDays();
				}
				
				else{
					$affiliateexpirationdays = $this->getDirectExpirationDays();
				}
				
				// Check If Past Affiliate Expiration (When New Affiliates Can Re-Capture Customer)
				if(empty($affiliateexpirationdays)){$affiliateexpirationdays = 999999;}
				if ($lastorderdays >= $affiliateexpirationdays){
					// Only Change Affiliates if Not Direct and Different Than Original
					if ($sess_affnetwork <> 'N/A' && (($sess_affnetwork<>$affnetwork) || (($sess_affnetwork == $affnetwork) && ($sess_affid <> $affid)))){
					$newaffiliate = true;
					}
				}
			}
			
			//----------------------------------------
			//Update Customer AND Variables if Needed
			//----------------------------------------
			if($newaffiliate){			
			
				//Replace Variables with Session Values
				$affstatus 	= $sess_affstatus;
				$affnetwork = $sess_affnetwork;
				$affid 		= $sess_affid;
				$affreqid 	= $sess_affreqid;

				//Replace Customer Database Values with Session Values
				$customer->setAffstatus($affstatus);
				$customer->setAffnetwork($affnetwork);
				$customer->setAffid($affid);
				$customer->setAffreqid($affreqid);
				$customer->setAffdate($curdatetime);
				
			}		
				
			//Update The Affiliate Log for all customers
			$afflog		= $afflog.$sess_afflog.$curdatetime." ORDER# ".$realorderId."|";
			$customer->setAfflog($afflog);
			$customer->save();

			//------------------------------------------------------------------------------------------------
			//Load Affiliate Network Defaults
			//------------------------------------------------------------------------------------------------
			if ($affnetwork == 'Cake' && $this->isCakeEnabled()){
				
				//Residual Commission
				$residualenabled = $this->isCakeResidualEnabled();		
				$residualexpirationdays01 = $this->getCakeResidualExpirationDays01();
				$residualexpirationdays02 = $this->getCakeResidualExpirationDays02();

				//Indirect Commission
				$indirectenabled = $this->isCakeIndirectEnabled();
				
				//Other Options
				$showzeropixel = $this->isCakeShowZeroPixel();
				
				//Make Trackable
				$trackable = true;

				
			}			
			
			elseif ($affnetwork == 'Share-A-Sale' && $this->isShareASaleEnabled()){
				
				//Residual Commission
				$residualenabled = $this->isShareASaleResidualEnabled();		
				$residualexpirationdays01 = $this->getShareASaleResidualExpirationDays01();
				$residualexpirationdays02 = $this->getShareASaleResidualExpirationDays02();

				//Indirect Commission
				$indirectenabled = $this->isShareASaleIndirectEnabled();
				
				//Other Options
				$showzeropixel = $this->isShareASaleShowZeroPixel();
				
				//Make Trackable
				$trackable = true;
			}
			
			elseif ($affnetwork == 'Ebay Enterprise' && $this->isEbayEnterpriseEnabled()){
				
				//Residual Commission
				$residualenabled = $this->isEbayEnterpriseResidualEnabled();				
				$residualexpirationdays01 = $this->getEbayEnterpriseResidualExpirationDays01();
				$residualexpirationdays02 = $this->getEbayEnterpriseResidualExpirationDays02();
				
				//Indirect Commission
				$indirectenabled = $this->isEbayEnterpriseIndirectEnabled();
				
				//Other Options
				$showzeropixel = $this->isEbayEnterpriseShowZeroPixel();
				
				//Make Trackable
				$trackable = true;
			}
			
			else {
				
				//Not A Trackable Network
				$trackable = false;
				
			}
			
			

			//------------------------------------------------------------------------------------------------
			//Update Magento Order
			//------------------------------------------------------------------------------------------------
						
			if($trackable){
				
			
				//Set Default Commissions
				$affcommissiontype = "No Commission";
				
				//-----------------------------------------						
				//Determine The Commission Type To Pay
				//-----------------------------------------	
					
				if($newaffiliate == true) {
					//-----------------------------------------						
					//Base Commission (New Affiliate)
					//-----------------------------------------						
					if($affstatus=="Indirect" && $indirectenabled){
					//InDirect Affiliate Base Commission
						$affcommissiontype = "Base Commission";
						$affnote = "Indirect - First Order";
						$affsku  = "INDORDER";
					}
					elseif ($affstatus=="Direct"){
					//Direct Affiliate Base Commission
						$affcommissiontype = "Base Commission";
						$affnote = "Direct - First Order";
						$affsku  = "DIRORDER";
					}					
				}
				
				
				else {
					//-----------------------------------------
					//Residual Commission (Existing Affiliate)
					//-----------------------------------------			
					
					$affiliatedays = floor((strtotime($curdatetime) - strtotime($affdate)) / (60 * 60 * 24));
					
					//Check which Residual To Apply
					if ($affiliatedays <= $residualexpirationdays01){
						if($affstatus=="Indirect" && $indirectenabled){
	
						//InDirect Affiliate Residual Commission
							$affcommissiontype = "Residual Commission (01)";
							$affcommission = $indirectresidualcommission;
							$affnote = "Indirect - Residual (01)";	
							$affsku  = "INDRESID01";						
						}
						elseif ($affstatus=="Direct"){
						//Direct Affiliate Residual Commission
							$affcommissiontype = "Residual Commission (01)";
							$affcommission = $residualcommission;
							$affnote = "Direct - Residual (01)";
							$affsku  = "DIRRESID01";		
						}					}
					
					elseif ($affiliatedays <= $residualexpirationdays02){
						if($affstatus=="Indirect" && $indirectenabled){
	
						//InDirect Affiliate Residual Commission
							$affcommissiontype = "Residual Commission (02)";
							$affcommission = $indirectresidualcommission;
							$affnote = "Indirect - Residual (02)";	
							$affsku  = "INDRESID02";						
						}
						elseif ($affstatus=="Direct"){
						//Direct Affiliate Residual Commission
							$affcommissiontype = "Residual Commission (02)";
							$affcommission = $residualcommission;
							$affnote = "Direct - Residual (02)";
							$affsku  = "DIRRESID02";		
						}						
					}
					
					else {
						//Beyond Residual Period
						$trackable = false;	
					}

				}
				
				//Save The Order Information
				$order->setAffnetwork($affnetwork);
				$order->setAffid($affid);	
				$order->setAffreqid($affreqid);	
				$order->setAffstatus($affstatus);
				$order->setAffcommissiontype($affcommissiontype);
				$order->save();
				
			}
			
			else{
				//Direct - No Network
				//$order->setAffreferral("Direct");
				//$order->save();	
			}				
			
			//------------------------------------------------------------------------------------------------
			//Show Tracking Pixel(s)
			//------------------------------------------------------------------------------------------------
			if($trackable){
				
				if($affnetwork == 'Cake'){
					//Cake Pixel 
					
					if ($affsku == 'DIRORDER'){
						$pixel = '<iframe src="https://trkhc.cake.aclz.net/p.ashx?o=2&e=1&r=' . $affreqid . '&t=' . $realorderId .'&ecst=[' . $_sub . ']&ecd=[' . $_discount . ']&ectx=[' . $_tax . ']&ecsh=[' .$_shipping . ']&ect=[' . $_grandtotal . '] height="1" width="1" frameborder="0"></iframe>';
					}
					elseif ($affsku == 'INDORDER'){
						$pixel = '<iframe src="https://trkhc.cake.aclz.net/p.ashx?o=2&e=3&r=' . $affreqid . '&t=' . $realorderId .'&ecst=[' . $_sub . ']&ecd=[' . $_discount . ']&ectx=[' . $_tax . ']&ecsh=[' .$_shipping . ']&ect=[' . $_grandtotal . ']" height="1" width="1" frameborder="0"></iframe>';	
					}
					elseif ($affsku == 'DIRRESID01'){
						$pixel = '<iframe src="https://trkhc.cake.aclz.net/p.ashx?o=2&e=5&r=' . $affreqid . '&t=' . $realorderId .'&ecst=[' . $_sub . ']&ecd=[' . $_discount . ']&ectx=[' . $_tax . ']&ecsh=[' .$_shipping . ']&ect=[' . $_grandtotal . ']" height="1" width="1" frameborder="0"></iframe>';	
					}
					elseif ($affsku == 'DIRRESID02'){
						$pixel = '<iframe src="https://trkhc.cake.aclz.net/p.ashx?o=2&e=2&r=' . $affreqid . '&t=' . $realorderId .'&ecst=[' . $_sub . ']&ecd=[' . $_discount . ']&ectx=[' . $_tax . ']&ecsh=[' .$_shipping . ']&ect=[' . $_grandtotal . ']" height="1" width="1" frameborder="0"></iframe>';	
					}
					elseif ($affsku == 'INDRESID01'){
						$pixel = '<iframe src="https://trkhc.cake.aclz.net/p.ashx?o=2&e=4&r=' . $affreqid . '&t=' . $realorderId .'&ecst=[' . $_sub . ']&ecd=[' . $_discount . ']&ectx=[' . $_tax . ']&ecsh=[' .$_shipping . ']&ect=[' . $_grandtotal . ']" height="1" width="1" frameborder="0"></iframe>';	
					}
					elseif ($affsku == 'INDRESID02'){
						$pixel = '<iframe src="https://trkhc.cake.aclz.net/p.ashx?o=2&e=6&r=' . $affreqid . '&t=' . $realorderId .'&ecst=[' . $_sub . ']&ecd=[' . $_discount . ']&ectx=[' . $_tax . ']&ecsh=[' .$_shipping . ']&ect=[' . $_grandtotal . ']" height="1" width="1" frameborder="0"></iframe>';	
					}
				}		
				
				elseif($affnetwork == 'Share-A-Sale'){
					//ShareASale Pixel 
					//if($affcommission > 0 || $showzeropixel==true){
						$pixel = '<img src=\'https://shareasale.com/sale.cfm?tracking=' . $realorderId . '+' . urlencode($affnote) . '&amount=' . $_total . '&transtype=sale&newcustomer=' . $newcustomer . '&merchantID=' . $this->getShareASaleMerchantId() . '&cart=' . urlencode($affnote);
						if ($affid){
							$pixel = $pixel . '&userID=' . $affid;
						}
						$pixel = $pixel . '\' width=\'1\' height=\'1\'>';
					//}
				}
				
				else {
					if($affnetwork == 'Ebay Enterprise'){
					//Ebay Enterprise Pixel
						if($affcommission > 0 || $showzeropixel==true){
							$pixel = '<iframe src=\'https://t.pepperjamnetwork.com/track?PID=' . $this->getEbayEnterpriseMerchantId() . '&INT=ITEMIZED&ITEM1='. urlencode($affsku) . '&QTY1=1&AMOUNT1=' . $_total . '&OID=' . $realorderId;
							$pixel = $pixel . '\' width=\'1\' height=\'1\' frameborder=\'0\' ></iframe>';
						}
					}
				}
				
				//Create Tag if Debug Mode
				if ($this->isDebugPixel()){
					$pixel = '<br/><br/><strong>Tracking Pixel(s):</strong><br/>'.htmlspecialchars($pixel);					
				}				
			}
			
			
		//-----------------------------------------
		// Clear Cookies
		//-----------------------------------------				
		$this->clearCookie();
		//Mage::log('Order#'.$realorderId.' Pixel: '.$pixel);
		
		//Return The Tracking Pixel
		return $pixel;			
			
		}

	}
	
	 //-----------------------------------------
	 // Functions
	 //-----------------------------------------		
	 public function getCustomerNetwork() {
        $_customerid = Mage::getSingleton('customer/session')->getCustomerId();
        if ($_customerid) {
			//Customer Checkout
            $customer = Mage::getModel('customer/customer')->load($_customerid);
		    $affnetwork = $customer->getAffnetwork();
        }
		else {
			//Guest Checkout
			$affnetwork = Mage::getSingleton('core/session')->getAffnetwork();
		}
		return $affnetwork;
    }

    public function getCustomerAffiliateId() {
        $customerid = Mage::getSingleton('customer/session')->getCustomerId();

        if ($customerid) {
			//Customer Checkout
            $customer = Mage::getModel('customer/customer')->load($customerid);
            $affid = $customer->getAffid();
        }
		else {
			//Guest Checkout
			$affid = Mage::getSingleton('core/session')->getAffid();
		}
        return $affid;
    }


	public function clearCookie() {
        //Removes All Affiliate Information
		$magsession = Mage::getSingleton('core/session');
		$magcookie = Mage::getSingleton('core/cookie');
		
		//Clear Session
		$magsession->unsAfflast();
		$magsession->unsAffstatus();
		$magsession->unsAffnetwork();
		$magsession->unsAffid();
		$magsession->unsAffreqid();
		$magsession->unsAfflog();

		//Clear Cookies
		$magcookie->delete('afflast');
		$magcookie->delete('affstatus');
		$magcookie->delete('affnetwork');
		$magcookie->delete('affid');
		$magcookie->delete('affreqid');
		$magcookie->delete('afflog');
    }

	
	public function showDebug(){
		if ($this->isEnabled()){
			if ($this->isDebugTracking()){
				$curdatetime = date('Y-m-d H:i:s');

				$magsession = Mage::getSingleton('core/session');
				$magcookie = Mage::getSingleton('core/cookie');
				
				$customersession = Mage::getSingleton('customer/session', array('name'=>'frontend'));
				$customerid = Mage::getSingleton('customer/session')->getCustomerId();
				$customer = Mage::getModel('customer/customer')->load($customerid);
				$ordercollection = Mage::getModel('sales/order')->getCollection()->addFilter('customer_id', $customerid)->setOrder('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC);
            	$count = $ordercollection->count();
				$lastorder = $ordercollection->getFirstItem();
				$lastorderdate = $lastorder->getData('created_at');
				$lastorderdays = floor((strtotime($curdatetime) - strtotime($lastorderdate)) / (60 * 60 * 24));

				//Hidden Debug Window
				$debug = "<div style=\"position:relative;display: block;width:1000px;margin-left:auto;margin-right:auto;\">";
				$debug = $debug."</div>";
				$debug = $debug."<div id=\"debugwindow\" style=\"display:block\">" ;


				//Actual Debug Window
				$debug = $debug."<div align=\"center\" style=\"background-color:white\">";
				$debug = $debug."<table><tr>";
				
				
				//Referral Information from Session
				$debug = $debug."<td style=\"vertical-align:top;padding:20px 20px 20px 20px\">";
				$debug = $debug."<table style=\"width:400px;\" class=\"debug\">";
				$debug = $debug."<tr><td colspan=\"2\" style=\"background-color:#999\">Session/Cookie Data</td></tr>";
				$debug = $debug."<tr><td colspan=\"2\" style=\"background-color:#CCC\">Referral Status</td></tr>";
				$debug = $debug."<tr><td>Last Referrer:</td><td>".$magsession->getAfflast()."</td></tr>";
				$debug = $debug."<tr><td>Commission Type:</td><td>".$magsession->getAffstatus()."</td></tr>";
				$debug = $debug."<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";	
				$debug = $debug."<tr><td colspan=\"2\" style=\"background-color:#CCC\">Commissioned Affiliate</td></tr>";
				if($magsession->getAffnetwork()=="N/A"){
					$debug = $debug."<tr><td colspan=\"2\">No Current Affiliate</td></tr>";
				}
				else{
					$debug = $debug."<tr><td>Affiliate Network:</td><td>".$magsession->getAffnetwork()."</td></tr>";
					$debug = $debug."<tr><td>Affiliate ID:</td><td>".$magsession->getAffid()."</td></tr>";
					$debug = $debug."<tr><td>Request ID:</td><td>".$magsession->getAffreqid()."</td></tr>";
				}
				$debug = $debug."<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
				$debug = $debug."</table>";
				$debug = $debug."</td>";
				
				//Referral Information from Cookie
				//$debug = $debug."<td style=\"vertical-align:top\">";
				//$debug = $debug."<table class=\"debug\">";
				//$debug = $debug."<tr><td colspan=\"2\" style=\"font-size:12px;background-color:#999;padding:3px 6px 3px 6px;color:black\">Cookie Data</td></tr>";
				//$debug = $debug."<tr><td colspan=\"2\" style=\"font-size:12px;background-color:#CCC;padding:3px 6px 3px 6px;color:black\">First Referral</td></tr>";
				//$debug = $debug."<tr><td>Initial Referral:</td><td>".$magcookie->get("affprimary")."</td></tr>";
				//$debug = $debug."<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";	
				//$debug = $debug."<tr><td colspan=\"2\" style=\"font-size:12px;background-color:#CCC;padding:3px 6px 3px 6px;color:black\">Commissioned Affiliate</td></tr>";
				//$debug = $debug."<tr><td>Affiliate Network:</td><td>".$magcookie->get("affnetwork")."</td></tr>";
				//$debug = $debug."<tr><td>Affiliate ID:</td><td>".$magcookie->get("affid")."</td></tr>";
				//$debug = $debug."</table>";
				//$debug = $debug."</td>";
			
				//Customer Information from Magento
				if($customersession->isLoggedIn()){			
					
					$curdatetime = date('Y-m-d H:i:s');

					$debug = $debug."<td style=\"vertical-align:top;padding:20px 20px 20px 20px\">";
					$debug = $debug."<table class=\"debug\">";
					$debug = $debug."<tr><td colspan=\"2\" style=\"background-color:#999\">Magento Customer Database</td></tr>";
					$debug = $debug."<tr><td colspan=\"2\" style=\"background-color:#CCC\">Referral Status</td></tr>";
					$debug = $debug."<tr><td>Referral Status:</td><td>".$customer->getAffstatus()."</td></tr>";
					$debug = $debug."<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";	
					$debug = $debug."<tr><td colspan=\"2\" style=\"background-color:#CCC\">Commissioned Affiliate</td></tr>";
					$debug = $debug."<tr><td>Affiliate Network:</td><td>".$customer->getAffnetwork()."</td></tr>";
					$debug = $debug."<tr><td>Affiliate ID:</td><td>".$customer->getAffid()."</td></tr>";
					$debug = $debug."<tr><td>Request ID:</td><td>".$customer->getAffreqid()."</td></tr>";
					$debug = $debug."<tr><td>Current Date:</td><td>".$curdatetime."</td></tr>";		
					$debug = $debug."<tr><td>Last Order Date:</td><td>".$lastorderdate."</td></tr>";
					$debug = $debug."<tr><td>Last Order Days:</td><td>".$lastorderdays."</td></tr>";			
					$debug = $debug."<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
					$debug = $debug."</table>";	
					$debug = $debug."</td>";
				}
				
				//Status/Event Table
				$debug = $debug."<td style=\"vertical-align:top;padding:20px 20px 20px 20px\">";
				$debug = $debug."<table class=\"debug\">";
				$debug = $debug."<tr><td colspan=\"2\" style=\"background-color:#999\">Status Monitor</td></tr>";

				//Status Table				
				$debug = $debug."<tr><td colspan=\"2\" style=\"background-color:#CCC\">Current Status</td></tr>";

				if(!$customersession->isLoggedIn() ||  $count==0){
				//Customer Not Logged In Or An Order Hasnt been placed yet
					if  ($magsession->getAffstatus()=="Direct"){
						$debug = $debug."<tr><td style=\"vertical-align:top\" colspan=\"2\">" . $magsession->getAffnetwork() . " Affiliate (#" . $magsession->getAffid() . ") is scheduled to receive a Direct Commission."		;
						$debug = $debug."</td></tr>";
					}
					elseif ($magsession->getAffstatus()=="Indirect" && $magsession->getAffnetwork()!="N/A"){
						$debug = $debug."<tr><td style=\"vertical-align:top\" colspan=\"2\">" . $magsession->getAffnetwork() . " Affiliate (#" . $magsession->getAffid() . ") is scheduled to receive an Indirect Commission.";		
						$debug = $debug."</td></tr>";	
					}
					else 
					{
						$debug = $debug."<tr><td style=\"vertical-align:top\" colspan=\"2\">There are currently no Affiliates scheduled to receive a commission.</td></tr>";
					}		
					
					//Event Table					
					$debug = $debug."<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
					$debug = $debug."<tr><td colspan=\"2\" style=\"background-color:#CCC\">Last Page Load Event</td></tr>";
					
					$debugevent = "";
					if($magsession->getAffcookieevent()!="No Event"){
						$debugevent = $debugevent."<tr><td colspan=\"2\" style=\"vertical-align:top\">".$magsession->getAffcookieevent()."</td></tr>";
						$debugevent = $debugevent."<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
					}
					
					if($magsession->getAffmagentoevent()!="No Event"){
						$debugevent = $debugevent."<tr><td colspan=\"2\" style=\"vertical-align:top\">".$magsession->getAffmagentoevent()."</td></tr>";
						$debugevent = $debugevent."<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
					}
					
					if($debugevent){
						$debug = $debug.$debugevent;
					}
					else{
						$debug = $debug."<tr><td colspan=\"2\" style=\"vertical-align:top\"><span style=\"color:blue\">No Change</span><br/>There were no Affiliate changes on the last Page Load.</td></tr>";
					}
					
				}
				else{
				//Customer Affiliate Information Was Already Saved Permanently
				//	if  ($customer->getAffprimary()=="Affiliate"){
				//		$debug = $debug."<tr><td style=\"vertical-align:top\" colspan=\"2\"><span style=\"color:blue\">Assigned Affiliate</span><br/>The " . $customer->getAffnetwork() . "Affiliate (#" . $customer->getAffid() . ") has been permanently assigned to this customer and can no longer be replaced. <br/><br/>This affiliate will receive residual commissions based on the Direct Commission Structure.";
				//		$debug = $debug."</td></tr>";
				//	}
				//	elseif  ($customer->getAffprimary()=="Affiliate (Override)"){
				//		$debug = $debug."<tr><td style=\"vertical-align:top\" colspan=\"2\"><span style=\"color:blue\">Assigned Affiliate</span><br/>The " . $customer->getAffnetwork() . "Affiliate (#" . $customer->getAffid() . ") has been permanently assigned to this customer and can no longer be replaced. <br/><br/>This affiliate will receive residual commissions based on the Indirect Commission Structure.";
				//		$debug = $debug."</td></tr>";
				//	}
				//	elseif ($customer->getAffprimary()=="Direct" && $customer->getAffnetwork()){
				//		$debug = $debug."<tr><td style=\"vertical-align:top\" colspan=\"2\"><span style=\"color:blue\">Assigned Affiliate</span><br/>The " . $customer->getAffnetwork() . "Affiliate (#" . $customer->getAffid() . ") has been permanently assigned to this customer and can no longer be replaced. <br/><br/>This affiliate will receive residual commissions based on the Indirect Commission Structure.";
				//		$debug = $debug."</td></tr>";	
				//	}
				//	else 
				//	{
				//		$debug = $debug."<tr><td style=\"vertical-align:top\" colspan=\"2\"><span style=\"color:blue\">No Affiliate Assigned</span><br/>There are no Affiliates assigned to this customer. <br/><br/>No future affiliates can be added, as the customer already placed their first order.</td></tr>";
				//	}	
				}	

				$debug = $debug."</table>";	
				$debug = $debug."</td>";
						
				$debug = $debug."</tr><tr>";
				
								
				$debug = $debug."<td style=\"vertical-align:top;padding:20px 20px 20px 20px\">";
				
				//Status/Event Table
				$debug = $debug."<table class=\"debug\">";
				$debug = $debug."<tr><td style=\"background-color:#999\">Referral History</td></tr>";
				$debug = $debug."<tr><td>".str_replace("|","<br/>",$magsession->getAfflog())."</td></tr>";
				$debug = $debug."</table>";	
			
				
				//End Outer Table
				$debug = $debug."</td>";
				$debug = $debug."</tr></table>";
				$debug = $debug."</div>";
				
				//End Hidden Debug Window
				$debug = $debug."</div>";
				
				
				//Clear Event Tracking
				$magsession->setAffcookieevent("No Event");
				$magcookie->set("affcookieevent", "No Event",  time() + 2000000000,"/");
				$magsession->setAffmagentoevent("No Event");
				$magcookie->set("affmagentoevent", "No Event",  time() + 2000000000,"/");
				
				return $debug;
			}
		}
	}
}

?>