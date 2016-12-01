<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Observer
 *
 * @author Administrator
 */
class Sunovisio_AffiliateNetworkConnector_Model_Observer {

    public function loadCookie($event) {
		if(Mage::helper('affiliatenetworkconnector')->isEnabled()){	
			//-------------------------------------------
			//Check if in Session Variable First
			//-------------------------------------------
			$magsession = Mage::getSingleton('core/session');
			$magcookie = Mage::getSingleton('core/cookie');
			$afflast = $magsession->getAfflast();
			$affstatus = $magsession->getAffstatus();
			$affnetwork = $magsession->getAffnetwork();
			$affid = $magsession->getAffid();
			$affreqid = $magsession->getAffreqid();
			$afflog = $magsession->getAfflog();

			//-------------------------------------------
			//Load Session From Cookie If Not In Session
			//-------------------------------------------
			// Primary Values
			if (!isset($afflast) || !$afflast) {
				if($magcookie->get("afflast")){
					$afflast = $magcookie->get("afflast");	
					$magsession->setAfflast($afflast);
				}
			}
			if($magcookie->get("affstatus")){$magsession->setAffstatus($magcookie->get("affstatus"));}
			
			// Affiliate Values
			if (!isset($affnetwork) || !$affnetwork) {
				if($magcookie->get("affnetwork")){
					$affnetwork = $magcookie->get("affnetwork");							
					$magsession->setAffnetwork($affnetwork);
				}
				if($magcookie->get("affid")){
					$affid = $magcookie->get("affid");
					$magsession->setAffid($affid);
				}
				if($magcookie->get("affreqid")){
					$affreqid = $magcookie->get("affreqid");
					$magsession->setAffreqid($affreqid);
				}			
			}
			
			// Log Values
			if (!isset($afflog) || !$afflog) {
				if($magcookie->get("afflog")){
					$afflog = $magcookie->get("afflog");	
					$magsession->setAfflog($afflog);
				}				
			}
			
			
			//-------------------------------------------
			//First Referral
			//-------------------------------------------
			// Important: Check After Session Variable updated from Cookie(if needed)
			if (!isset($afflast) || !$afflast) {
				$firstReferral=true;
			}
			else{
				$firstReferral=false;
			}			
		
			//-------------------------------------------
			// Get Referring URL 
			//-------------------------------------------
			if(!empty($_SERVER['HTTP_REFERER'])){
				$refUrl = strtolower($_SERVER['HTTP_REFERER']);
			}
			else{
				$refUrl = "N/A";	
			}
			
			//-------------------------------------------
			// Get Current URL 
			//-------------------------------------------
			$curUrl = Mage::helper('core/url')->getCurrentUrl();
			$new_source = "";
			$new_affid = "";
			
			if(isset($curUrl)){
				$curUrl = strtolower($curUrl);
				$curUrl_parsed = parse_url($curUrl);
				if (isset($curUrl_parsed['query'])){parse_str($curUrl_parsed['query'], $curUrl_parts);}
				if (!empty($curUrl_parts['source'])){$new_source = $curUrl_parts['source'];}
				if (!empty($curUrl_parts['aid'])){$new_affid = $curUrl_parts['aid'];}
				if (!empty($curUrl_parts['rid'])){$new_affreqid = $curUrl_parts['rid'];}
			}
			else{
				$curUrl = "N/A";	
			}
			
			//-------------------------------------------
			//Set Default Variables
			//-------------------------------------------
			$curdatetime = Mage::getModel('core/date')->date('Y-m-d H:i:s');
			$networkpriority = Mage::helper('affiliatenetworkconnector')->getNetworkPriority();
			$networkindirectoverride = Mage::helper('affiliatenetworkconnector')->isNetworkIndirectOverride();
			$affcookieevent = "";
			$saveprimary = false;
			$savenetwork = false;
			
			
			//-------------------------------------------
			//Get Network Updates If Required
			//-------------------------------------------
			
			//---------------------------
			// Cake Affiliate
			//---------------------------
			if ($new_source=="cake" && Mage::helper('affiliatenetworkconnector')->isCakeEnabled()) {
				$new_affnetwork = "Cake";
				$indirectenabled = Mage::helper('affiliatenetworkconnector')->isCakeIndirectEnabled();
				$affpriority = Mage::helper('affiliatenetworkconnector')->getCakePriority();
				$organicindirectoverride = Mage::helper('affiliatenetworkconnector')->isCakeOrganicIndirectOverride();
				$affindirectoverride = Mage::helper('affiliatenetworkconnector')->isCakeAffiliateIndirectOverride();
				
				if($firstReferral){
					//First Referral
					$afflast = "Affiliate";
					$affstatus = "Direct";
					$saveprimary = true; 
					$savenetwork = true;
					$affcookieevent="<span style=\"color:red\">Direct Affiliate Added</span><br/>Cake Affiliate was the first referral to site.";
					$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
				}
				elseif($afflast == "Organic" && $affpriority=="Last" && $indirectenabled && $organicindirectoverride){
					//Indirect Referral
					$afflast = "Affiliate";
					$affstatus = "Indirect";					
					$saveprimary = true; 
					$savenetwork = true; 
					$affcookieevent="<span style=\"color:red\">Indirect Affiliate Added</span><br/>Cake Affiliate referred customer after they had already found site.";
					$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
				}
				elseif($afflast == "Organic" && $affpriority=="Last"){
					//Direct Referral (Last Affiliate Wins)
					$afflast = "Affiliate";	
					$affstatus = "Direct";		
					$saveprimary = true; 
					$savenetwork = true;
					$affcookieevent="<span style=\"color:red\">Direct Affiliate Added (Indirect Turned Off)</span><br/>Cake Affiliate referred customer after they had already found site.";
					$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
				}	
				elseif($afflast == "Organic" && $affpriority=="First"){
					//Direct Referral (Indirect Turned Off)
					$affcookieevent="<span style=\"color:red\">No Change (First Affiliate (Organic) Wins)</span><br/>New Cake affiliate has made a referral but no change was made.";
					$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
				}	
				
				elseif($affnetwork=="Cake" && $affpriority=="Last"){
					//Override Affiliate In Same Network
					$savenetwork = true; 
					if($afflast=="Affiliate"){
						//Override Direct Affiliate
						if(($affid != $new_affid) && $indirectenabled && $affindirectoverride){
							//Override Affiliate / Change Structure To Indirect
							$afflast = "Affiliate";
							$affstatus = "Indirect";
							$saveprimary = true; 
							$affcookieevent="<span style=\"color:red\">Direct Affiliate Replaced (Last Affiliate Wins)</span><br/>New Cake Affiliate has replaced previous Cake Affiliate.<br/><br/><span style=\"color:red\">Changed To Indirect Affiliate</span><br/>The new affiliate was not solely responsible for this sale.";
							$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
						}
						elseif ($affid != $new_affid){
							//Override Affiliate / No Change In Structure
							$savenetwork = true; 
							$affcookieevent="<span style=\"color:red\">".$affstatus." Affiliate Replaced (Last Affiliate Wins)</span><br/>New Cake Affiliate has replaced previous Cake Affiliate.";
							$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
						}
						else{
							// Current Affiliate is same as previous Affiliate
							$affcookieevent="<span style=\"color:red\">Same Affiliate</span><br/>Current Cake Affiliate is same as previous Cake Affiliate. No change was made.";
							$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
						}
					}
					else{
						//Unknown Event
						$affcookieevent="<span style=\"color:red\">Unknown Status</span><br/>TroubleShoot (01)";
					}
				}
				elseif($affnetwork=="Cake" && $affpriority=="First"){
						if ($affid != $new_affid){
							//No change, First Affiliate Wins
							$affcookieevent="<span style=\"color:red\">No Change (First Affiliate Wins)</span><br/>New Cake affiliate has made a referral but no change was made.</span>";
							$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
						}
						else{
							// Current Affiliate is same as previous Affiliate
							$affcookieevent="<span style=\"color:red\">No Change (First Affiliate Wins)</span><br/>Current referring Cake Affiliate is same as previous Cake Affiliate. No change was made.";
							$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
						}					
				}						
				elseif(!$firstReferral && $affnetwork!="N/A" && $affnetwork!="Cake" && $networkpriority=="Last"){
					//Override Network
					$savenetwork = true; 
					if($afflast=="Affiliate"){
						//Override Direct Affiliate
						if($networkindirectoverride){
							//Override Affiliate / Change Structure To Indirect
							$afflast = "Affiliate";
							$affstatus = "Indirect"	;						
							$saveprimary = true; 
							$savenetwork = true;
							$affcookieevent="<span style=\"color:red\">Direct Affiliate Replaced (Last Network Wins)</span><br/>New Cake Affiliate has replaced previous " . $affnetwork . " Affiliate.<br/><br/><span style=\"color:red\">Changed To Indirect Affiliate</span><br/>The new affiliate was not solely responsible for this sale.";
							$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
						}
						else{
							//Override Affiliate / No Change In Structure
							$savenetwork = true;
							$affcookieevent="<span style=\"color:red\">".$affstatus."  Affiliate Replaced (Last Network Wins)</span><br/>New Cake Affiliate has replaced previous " . $affnetwork . " Affiliate.";
							$afflog = $afflog.$curdatetime." CAKE Referral (".$new_affid.")|";
						}
						
					}
					else{
						//Unknown Event
						$affcookieevent="<span style=\"color:red\">Unknown Status</span><br/>TroubleShoot (02)";

					}
				}
				
			} 			
			
			//---------------------------
			// Share-A-Sale Affiliate
			//---------------------------
			elseif ($new_source=="sas" && Mage::helper('affiliatenetworkconnector')->isShareASaleEnabled()) {
				$new_affnetwork = "Share-A-Sale";
				$new_affreqid = "N/A";
				$indirectenabled = Mage::helper('affiliatenetworkconnector')->isShareASaleIndirectEnabled();
				$affpriority = Mage::helper('affiliatenetworkconnector')->getShareASalePriority();
				$organicindirectoverride = Mage::helper('affiliatenetworkconnector')->isShareASaleOrganicIndirectOverride();
				$affindirectoverride = Mage::helper('affiliatenetworkconnector')->isShareASaleAffiliateIndirectOverride();
				
				if($firstReferral){
					//First Referral
					$afflast = "Affiliate";
					$affstatus = "Direct";
					$saveprimary = true; 
					$savenetwork = true;
					$affcookieevent="<span style=\"color:red\">Direct Affiliate Added</span><br/>ShareASale Affiliate was the first referral to site.";
					$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
				}
				elseif($afflast == "Organic" && $affpriority=="Last" && $indirectenabled && $organicindirectoverride){
					//Indirect Referral
					$afflast = "Affiliate";
					$affstatus = "Indirect";					
					$saveprimary = true; 
					$savenetwork = true; 
					$affcookieevent="<span style=\"color:red\">Indirect Affiliate Added</span><br/>ShareASale Affiliate referred customer after they had already found site.";
					$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
				}
				elseif($afflast == "Organic" && $affpriority=="Last"){
					//Direct Referral (Last Affiliate Wins)
					$afflast = "Affiliate";	
					$affstatus = "Direct";		
					$saveprimary = true; 
					$savenetwork = true;
					$affcookieevent="<span style=\"color:red\">Direct Affiliate Added (Indirect Turned Off)</span><br/>ShareASale Affiliate referred customer after they had already found site.";
					$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
				}	
				elseif($afflast == "Organic" && $affpriority=="First"){
					//Direct Referral (Indirect Turned Off)
					$affcookieevent="<span style=\"color:red\">No Change (First Affiliate (Organic) Wins)</span><br/>New ShareASale affiliate has made a referral but no change was made.";
					$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
				}	
				
				elseif($affnetwork=="Share-A-Sale" && $affpriority=="Last"){
					//Override Affiliate In Same Network
					$savenetwork = true; 
					if($afflast=="Affiliate"){
						//Override Direct Affiliate
						if(($affid != $new_affid) && $indirectenabled && $affindirectoverride){
							//Override Affiliate / Change Structure To Indirect
							$afflast = "Affiliate";
							$affstatus = "Indirect";
							$saveprimary = true; 
							$affcookieevent="<span style=\"color:red\">Direct Affiliate Replaced (Last Affiliate Wins)</span><br/>New ShareASale Affiliate has replaced previous ShareASale Affiliate.<br/><br/><span style=\"color:red\">Changed To Indirect Affiliate</span><br/>The new affiliate was not solely responsible for this sale.";
							$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
						}
						elseif ($affid != $new_affid){
							//Override Affiliate / No Change In Structure
							$savenetwork = true; 
							$affcookieevent="<span style=\"color:red\">".$affstatus." Affiliate Replaced (Last Affiliate Wins)</span><br/>New ShareASale Affiliate has replaced previous ShareASale Affiliate.";
							$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
						}
						else{
							// Current Affiliate is same as previous Affiliate
							$affcookieevent="<span style=\"color:red\">Same Affiliate</span><br/>Current ShareASale Affiliate is same as previous ShareASale Affiliate. No change was made.";
							$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
						}
					}
					else{
						//Unknown Event
						$affcookieevent="<span style=\"color:red\">Unknown Status</span><br/>TroubleShoot (01)";
					}
				}
				elseif($affnetwork=="Share-A-Sale" && $affpriority=="First"){
						if ($affid != $new_affid){
							//No change, First Affiliate Wins
							$affcookieevent="<span style=\"color:red\">No Change (First Affiliate Wins)</span><br/>New ShareASale affiliate has made a referral but no change was made.</span>";
							$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
						}
						else{
							// Current Affiliate is same as previous Affiliate
							$affcookieevent="<span style=\"color:red\">No Change (First Affiliate Wins)</span><br/>Current referring ShareASale Affiliate is same as previous ShareASale Affiliate. No change was made.";
							$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
						}					
				}						
				elseif(!$firstReferral && $affnetwork!="N/A" && $affnetwork!="Share-A-Sale" && $networkpriority=="Last"){
					//Override Network
					$savenetwork = true; 
					if($afflast=="Affiliate"){
						//Override Direct Affiliate
						if($networkindirectoverride){
							//Override Affiliate / Change Structure To Indirect
							$afflast = "Affiliate";
							$affstatus = "Indirect"	;						
							$saveprimary = true; 
							$savenetwork = true;
							$affcookieevent="<span style=\"color:red\">Direct Affiliate Replaced (Last Network Wins)</span><br/>New ShareASale Affiliate has replaced previous " . $affnetwork . " Affiliate.<br/><br/><span style=\"color:red\">Changed To Indirect Affiliate</span><br/>The new affiliate was not solely responsible for this sale.";
							$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
						}
						else{
							//Override Affiliate / No Change In Structure
							$savenetwork = true;
							$affcookieevent="<span style=\"color:red\">".$affstatus."  Affiliate Replaced (Last Network Wins)</span><br/>New ShareASale Affiliate has replaced previous " . $affnetwork . " Affiliate.";
							$afflog = $afflog.$curdatetime." SAS Referral (".$new_affid.")|";
						}
						
					}
					else{
						//Unknown Event
						$affcookieevent="<span style=\"color:red\">Unknown Status</span><br/>TroubleShoot (02)";

					}
				}
				
			} 			
			
			//---------------------------
			// Ebay Enterprise Affiliate
			//---------------------------
			elseif ($new_source=="pjn" && Mage::helper('affiliatenetworkconnector')->isEbayEnterpriseEnabled()) {
				$new_affnetwork = "Ebay Enterprise";
				$new_affreqid = "N/A";
				$indirectenabled = Mage::helper('affiliatenetworkconnector')->isEbayEnterpriseIndirectEnabled();
				$affpriority = Mage::helper('affiliatenetworkconnector')->getEbayEnterprisePriority();
				$organicindirectoverride = Mage::helper('affiliatenetworkconnector')->isEbayEnterpriseOrganicIndirectOverride();
				$affindirectoverride = Mage::helper('affiliatenetworkconnector')->isEbayEnterpriseAffiliateIndirectOverride();
				
				if($firstReferral){
					//First Referral
					$afflast = "Affiliate";
					$affstatus = "Direct";
					$saveprimary = true; 
					$savenetwork = true;
					$affcookieevent="<span style=\"color:red\">Direct Affiliate Added</span><br/>Ebay Affiliate was the first referral to site.";
					$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
				}
				elseif($afflast == "Organic" && $affpriority=="Last" && $indirectenabled && $organicindirectoverride){
					//Indirect Referral
					$afflast = "Affiliate";
					$affstatus = "Indirect";					
					$saveprimary = true; 
					$savenetwork = true; 
					$affcookieevent="<span style=\"color:red\">Indirect Affiliate Added</span><br/>Ebay referred customer after they had already found site.";
					$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
				}
				elseif($afflast == "Organic" && $affpriority=="Last"){
					//Direct Referral (Last Affiliate Wins)
					$afflast = "Affiliate";	
					$affstatus = "Direct";		
					$saveprimary = true; 
					$savenetwork = true;
					$affcookieevent="<span style=\"color:red\">Direct Affiliate Added (Indirect Turned Off)</span><br/>Ebay Affiliate referred customer after they had already found site.";
					$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
				}	
				elseif($afflast == "Organic" && $affpriority=="First"){
					//Direct Referral (Indirect Turned Off)
					$affcookieevent="<span style=\"color:red\">No Change (First Affiliate (Organic) Wins)</span><br/>New Ebay affiliate has made a referral but no change was made.";
					$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
				}	
				
				elseif($affnetwork=="Ebay Enterprise" && $affpriority=="Last"){
					//Override Affiliate In Same Network
					$savenetwork = true; 
					if($afflast=="Affiliate"){
						//Override Direct Affiliate
						if(($affid != $new_affid) && $indirectenabled && $affindirectoverride){
							//Override Affiliate / Change Structure To Indirect
							$afflast = "Affiliate";
							$affstatus = "Indirect";
							$saveprimary = true; 
							$affcookieevent="<span style=\"color:red\">Direct Affiliate Replaced (Last Affiliate Wins)</span><br/>New Ebay Affiliate has replaced previous Ebay Affiliate.<br/><br/><span style=\"color:red\">Changed To Indirect Affiliate</span><br/>The new affiliate was not solely responsible for this sale.";
							$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
						}
						elseif ($affid != $new_affid){
							//Override Affiliate / No Change In Structure
							$savenetwork = true; 
							$affcookieevent="<span style=\"color:red\">".$affstatus." Affiliate Replaced (Last Affiliate Wins)</span><br/>New Ebay Affiliate has replaced previous Ebay Affiliate.";
							$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
						}
						else{
							// Current Affiliate is same as previous Affiliate
							$affcookieevent="<span style=\"color:red\">Same Affiliate</span><br/>Current Ebay Affiliate is same as previous Ebay Affiliate. No change was made.";
							$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
						}
					}
					else{
						//Unknown Event
						$affcookieevent="<span style=\"color:red\">Unknown Status</span><br/>TroubleShoot (01)";
					}
				}
				elseif($affnetwork=="Ebay Enterprise" && $affpriority=="First"){
						if ($affid != $new_affid){
							//No change, First Affiliate Wins
							$affcookieevent="<span style=\"color:red\">No Change (First Affiliate Wins)</span><br/>New Ebay affiliate has made a referral but no change was made.</span>";
							$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
						}
						else{
							// Current Affiliate is same as previous Affiliate
							$affcookieevent="<span style=\"color:red\">No Change (First Affiliate Wins)</span><br/>Current referring Ebay Affiliate is same as previous Ebay Affiliate. No change was made.";
							$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
						}					
				}						
				elseif(!$firstReferral && $affnetwork!="N/A" && $affnetwork!="Ebay Enterprise" && $networkpriority=="Last"){
					//Override Network
					$savenetwork = true; 
					if($afflast=="Affiliate"){
						//Override Direct Affiliate
						if($networkindirectoverride){
							//Override Affiliate / Change Structure To Indirect
							$afflast = "Affiliate";
							$affstatus = "Indirect"	;						
							$saveprimary = true; 
							$savenetwork = true;
							$affcookieevent="<span style=\"color:red\">Direct Affiliate Replaced (Last Network Wins)</span><br/>New Ebay Affiliate has replaced previous " . $affnetwork . " Affiliate.<br/><br/><span style=\"color:red\">Changed To Indirect Affiliate</span><br/>The new affiliate was not solely responsible for this sale.";
							$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
						}
						else{
							//Override Affiliate / No Change In Structure
							$savenetwork = true;
							$affcookieevent="<span style=\"color:red\">".$affstatus."  Affiliate Replaced (Last Network Wins)</span><br/>New Ebay Affiliate has replaced previous " . $affnetwork . " Affiliate.";
							$afflog = $afflog.$curdatetime." EBAY Referral (".$new_affid.")|";
						}
						
					}
					else{
						//Unknown Event
						$affcookieevent="<span style=\"color:red\">Unknown Status</span><br/>TroubleShoot (02)";

					}
				}
				
			} 			
					
			
			//---------------------------
			// No Affiliate Referral
			//---------------------------
			elseif ($firstReferral){
				// First Referral : Direct
				$afflast = "Organic";	
				$affstatus = "N/A";	
				$saveprimary = true;

				//Set Affiliate Defaults
				$new_affnetwork = "N/A";
				$new_affid = "N/A";		
				$new_affreqid = "N/A";				
				$savenetwork = true;
				
				$affcookieevent="<span style=\"color:blue\">Direct Referral (No Affiliate)</span>";
				$afflog = $afflog.$curdatetime." ORGANIC Referral|";
			}


			//-----------------------------------------------------
			//Update Cookie/Session With Primary Values If Required
			//-----------------------------------------------------	
			if ($saveprimary){
				//First Click To Site
				$magsession->setAfflast($afflast);
				$magsession->setAffstatus($affstatus);
				
				$magcookie->set("afflast", $afflast,  time() + 2000000000,"/");
				$magcookie->set("affstatus", $affstatus,  time() + 2000000000,"/");
			}

			//-----------------------------------------------------
			//Update Cookie/Session With Network Values If Required
			//-----------------------------------------------------			
			if ($savenetwork) {
				//Set Affiliate Network Values
				$magsession->setAffnetwork($new_affnetwork);
				$magsession->setAffid($new_affid);
				$magsession->setAffreqid($new_affreqid);

				$magcookie->set("affnetwork", $new_affnetwork,  time() + 2000000000,"/");
				$magcookie->set("affid", $new_affid,  time() + 2000000000,"/");
				$magcookie->set("affreqid", $new_affreqid,  time() + 2000000000,"/");
			}
			
			//-----------------------------------------------------
			//Update Tracking / Logging
			//-----------------------------------------------------			
			// Event Tracking
			if (Mage::helper('affiliatenetworkconnector')->isDebugTracking()){
				$magsession->setAffcookieevent($affcookieevent);
				$magcookie->set("affcookieevent", $affcookieevent,  time() + 2000000000,"/");
			}
			//Logging
			$magsession->setAfflog($afflog);
			$magcookie->set("afflog", $afflog,  time() + 2000000000,"/");
			
			
			
		}
   }

}

?>