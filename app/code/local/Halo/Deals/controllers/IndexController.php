<?php
class Halo_Deals_IndexController extends Mage_Core_Controller_Front_Action
{

    public function IndexAction()
    {

        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Deals"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home Page"),
            "title" => $this->__("Home Page"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("deals", array(
            "label" => $this->__("Deals"),
            "title" => $this->__("Deals")
        ));

        $this->renderLayout();

    }
    public function postAction()
    {
      // echo "1";exit;
        $post_data = $this->getRequest()->getPost();
        if ($post_data) {

            try {
                if ($post_data['country'] != "United States") {
                    $post_data['state'] = $post_data['state1'];
                }
                if ($post_data['deal_name'] == 1) {
                    $post_data['battery_color'] = 0;
                    $post_data['sample_pack'] = 0;
                }
                if ($post_data['deal_name'] == 0) {
                    $post_data['flavor_option_2'] = 0;
                    $post_data['fax'] = 0;
                    $post_data['sample_pack'] = 0;
                }
                if ($post_data['deal_name'] == 2) {
                    $post_data['battery_color'] = 0;
                    $post_data['flavor_option_2'] = 0;
                    $post_data['flavor_option_1'] = 0;

                }

                // echo $post_data['samplepack'];exit;

                $coupon_code=$_REQUEST['voucher'];
                $codeLength = strlen($coupon_code);
                $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;
                if ($codeLength) {

                    $db1 = Mage::getSingleton('core/resource')->getConnection('core_read');
                     $sql1 = "SELECT b.discount_amount,a.code, a.usage_limit, a.usage_per_customer, a.times_used, a.expiration_date, b.is_active , b.from_date, b.to_date
                    FROM salesrule_coupon AS a
                    INNER JOIN salesrule AS b ON a.rule_id = b.rule_id
                    WHERE a.code = ?";
                    $sql1 = $db1->quoteInto($sql1, $coupon_code);
                    $rows1 = $db1->fetchAll($sql1);

                    $sql2 = "SELECT voucher FROM deals_gp_sl WHERE voucher = ?";
                    $sql2 = $db1->quoteInto($sql2, $coupon_code);
                    $rows2 = $db1->fetchAll($sql2); 

                    $expiry= $rows1[0]['expiration_date'];
                    $today = date("Y-m-d");
                    $todaysDate = strtotime($today);
                    $expiryDate = strtotime($expiry);
                    //echo $isCodeLengthValid.'--'.$coupon_code.'--'.$rows1[0]['code'].'--'.$rows1[0]['is_active'].'--'.$rows2[0]['voucher'].'!--'.$coupon_code.'--'.$expiryDate.'--'.$todaysDate;exit;    

                    //if ($isCodeLengthValid && ($coupon_code == $rows1[0]['code']) && ($rows1[0]['is_active']==1) && ($rows2[0]['voucher']!=$coupon_code) && ($expiryDate>$todaysDate)) {
                   /* if ($isCodeLengthValid && ($coupon_code == $rows1[0]['code']) && ($rows1[0]['is_active']==1) && ($rows2[0]['voucher']!=$coupon_code) && ($expiryDate>$todaysDate)) {*/
                   $fromdate= $rows1[0]['from_date'];
                   $todate= $rows1[0]['to_date'];
                $paymentDate = new DateTime(); // Today
                $contractDateBegin = new DateTime($fromdate);
                $contractDateEnd  = new DateTime($todate);
                                                                                                                    
                if ($isCodeLengthValid && ($coupon_code == $rows1[0]['code']) && ($rows1[0]['is_active']==1) && ($rows2[0]['voucher']!=$coupon_code) && ($paymentDate->getTimestamp() > $contractDateBegin->getTimestamp() && $paymentDate->getTimestamp() < $contractDateEnd->getTimestamp()) ) {
                  
                        Mage::getSingleton('core/session')->addSuccess(
                            $this->__('Voucher code "%s" was applied.', Mage::helper('core')->escapeHtml($coupon_code))
                        );

                        $model    = Mage::getModel("deals/deals")->addData($post_data)->setId($this->getRequest()->getParam("id"))->save();
                        $insertId = $model->save()->getId();


                        /* send an email template*/
                        // This is the template name from your etc/config.xml 
                        if ($post_data['deal_name'] == 0) {
                            $template_id = 'Halo Groupon Voucher Redeem Information';
                        }else if ($post_data['deal_name'] == 2) {
                            $template_id = 'e-Liquid Sample Pack Voucher Redeem Information';
                        }else{
                            $template_id = 'Halo Living Social Voucher Redeem Information';
                        }
                        $email_to = $this->getRequest()->getPost('last_name');
                        $customer_name   = $this->getRequest()->getPost('first_name');
                        $typeofkit=$this->getRequest()->getPost('type_of_kit');
                        if($typeofkit==1){ $typeofkit = "Tobacco - Tribeca";}
                        elseif($typeofkit==2){$typeofkit = "Menthol - Mystic";}
                        elseif($typeofkit==3){$typeofkit = "Tobacco - Tribeca";}
                        elseif($typeofkit==4){$typeofkit = "Menthol - SubZero";}
                        else{$typeofkit = "N/A";}

                        $sample_pack=$this->getRequest()->getPost('sample_pack');
                        if($sample_pack==1){ $sample_pack = "Caf&eacute; Sample Pack";}
                        elseif($sample_pack==2){$sample_pack = "Tobacco/Menthol Variety Sample Pack";}
                        elseif($sample_pack==3){$sample_pack = "Harvest Sample Pack";}
                        else{$sample_pack = "N/A";}

                        //echo $post_data['deal_name']; exit;

                        $flavor_option_1=$this->getRequest()->getPost('flavor_option_1');
                        if($flavor_option_1==1){ $flavor_option_1 = "Tribeca (sweet tobacco)";}
                        elseif($flavor_option_1==2){$flavor_option_1 = "Torque 56 (classic tobacco)";}
                        elseif($flavor_option_1==3){$flavor_option_1 = "Malibu (pina colada)";}
                        elseif($flavor_option_1==4){$flavor_option_1 = "SubZero (X strength menthol)";}
                        elseif($flavor_option_1==5){$flavor_option_1 = "Belgian Cocoa (chocolate)";}
                        elseif($flavor_option_1==6){$flavor_option_1 = "Kringle's Curse (peppermint)";}
                        elseif($flavor_option_1==7){ $flavor_option_1 = "Tobacco - Tribeca";}
                        elseif($flavor_option_1==8){$flavor_option_1 = "Menthol - SubZero";}
                        else{$flavor_option_1 = "N/A";}
                       
                        $flavor_option_2=$this->getRequest()->getPost('flavor_option_2');
                        if($flavor_option_2==1){ $flavor_option_2 = "Tribeca (sweet tobacco)";}
                        elseif($flavor_option_2==2){$flavor_option_2 = "Torque 56 (classic tobacco)";}
                        elseif($flavor_option_2==3){$flavor_option_2 = "Malibu (pina colada)";}
                        elseif($flavor_option_2==4){$flavor_option_2 = "SubZero (X strength menthol)";}
                        elseif($flavor_option_2==5){$flavor_option_2 = "Belgian Cocoa (chocolate)";}
                        elseif($flavor_option_2==6){$flavor_option_2 = "Kringle's Curse (peppermint)";}
                        else{$flavor_option_2 = "N/A";}

                        $battery_color=$this->getRequest()->getPost('battery_color');
                        if($battery_color==1){ $battery_color = "Black";}
                        elseif($battery_color==2){$battery_color = "Titanium";}
                        elseif($battery_color==3){$battery_color = "Red";}
                        elseif($battery_color==4){$battery_color = "Blue";}
                        elseif($battery_color==5){$battery_color = "Iridescence";}
                        else{$battery_color = "N/A";}

                        $nicotinelevel=$this->getRequest()->getPost('fax');
                        if($nicotinelevel==1){ $nicotinelevel = "00MG: No Nicotine";}
                        elseif($nicotinelevel==2){$nicotinelevel = "06MG: Super Light Strength";}
                        elseif($nicotinelevel==3){$nicotinelevel = "12MG: Light Strength";}
                        elseif($nicotinelevel==4){$nicotinelevel = "18MG: Regular Strength";}
                        elseif($nicotinelevel==5){$nicotinelevel = "24MG: X-Strength";}
                        else{$nicotinelevel = "N/A";}

                        // Load our template by template_id
                        $emailTemplate  = Mage::getModel('core/email_template')->loadByCode($template_id);
                        // Here is where we can define custom variables to go in our email template!
                        if($post_data['deal_name']==0){
                            $email_template_variables = array(
                                'voucher' => $this->getRequest()->getPost('voucher'),
                                'battery_color' => $battery_color,
                                'sample_pack' => $sample_pack,
                                'flavor_option_1' => $flavor_option_1,                        
                                'first_name' => $this->getRequest()->getPost('first_name'),
                                'address' => $this->getRequest()->getPost('address'),
                                'company' => $this->getRequest()->getPost('company'),
                                'city' => $this->getRequest()->getPost('city'),
                                'zip' => $this->getRequest()->getPost('zip'),
                                'state' => $this->getRequest()->getPost('state'),
                                'state1' => $this->getRequest()->getPost('state1'),
                                'country' => $this->getRequest()->getPost('country'),
                                'last_name' => $this->getRequest()->getPost('last_name')
                                // Other variables for our email template.
                            );
                            // I'm using the Store Name as sender name here.
                            $sender_name = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME);
                            // I'm using the general store contact here as the sender email.
                            $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
                            $emailTemplate->setSenderName($sender_name);
                            $emailTemplate->setSenderEmail($sender_email); 

                            //Send the email!
                            $emailTemplate->send($email_to, $customer_name, $email_template_variables);
                        } else if($post_data['deal_name']==2){
                            $email_template_variables = array(
                                'voucher' => $this->getRequest()->getPost('voucher'),
                                'battery_color' => $battery_color,
                                'sample_pack'=> $sample_pack,
                                'fax'=> $nicotinelevel,
                                'flavor_option_1' => $flavor_option_1, 
                                'flavor_option_2' => $flavor_option_2,                       
                                'first_name' => $this->getRequest()->getPost('first_name'),
                                'address' => $this->getRequest()->getPost('address'),
                                'company' => $this->getRequest()->getPost('company'),
                                'city' => $this->getRequest()->getPost('city'),
                                'zip' => $this->getRequest()->getPost('zip'),
                                'state' => $this->getRequest()->getPost('state'),
                                'state1' => $this->getRequest()->getPost('state1'),
                                'country' => $this->getRequest()->getPost('country'),
                                'last_name' => $this->getRequest()->getPost('last_name')
                                // Other variables for our email template.
                            );
                            //print_r($email_template_variables);exit;
                            // I'm using the Store Name as sender name here.
                            $sender_name = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME);
                            // I'm using the general store contact here as the sender email.
                            $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
                            $emailTemplate->setSenderName($sender_name);
                            $emailTemplate->setSenderEmail($sender_email); 

                            //Send the email!
                            $emailTemplate->send($email_to, $customer_name, $email_template_variables);
                        }else{
                            $email_template_variables = array(
                                'voucher' => $this->getRequest()->getPost('voucher'),
                                'type_of_kit' => $typeofkit,
                                'flavor_option_1' => $flavor_option_1,
                                'flavor_option_2' => $flavor_option_2,
                                'fax' => $nicotinelevel,
                                'battery_color' => $battery_color,
                                'sample_pack' => $sample_pack,
                                'first_name' => $this->getRequest()->getPost('first_name'),
                                'address' => $this->getRequest()->getPost('address'),
                                'company' => $this->getRequest()->getPost('company'),
                                'city' => $this->getRequest()->getPost('city'),
                                'zip' => $this->getRequest()->getPost('zip'),
                                'state' => $this->getRequest()->getPost('state'),
                                'state1' => $this->getRequest()->getPost('state1'),
                                'country' => $this->getRequest()->getPost('country'),
                                'last_name' => $this->getRequest()->getPost('last_name')
                                // Other variables for our email template.
                            );
                            // I'm using the Store Name as sender name here.
                            $sender_name = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME);
                            // I'm using the general store contact here as the sender email.
                            $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
                            $emailTemplate->setSenderName($sender_name);
                            $emailTemplate->setSenderEmail($sender_email); 

                            //Send the email!
                            $emailTemplate->send($email_to, $customer_name, $email_template_variables);
                        }
                        /*print_r($email_template_variables);exit;*/


                        /* send an email template*/
                        $message  = $this->__('Data Saved Successfully');
                        $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
                        $this->_redirectUrl($url."&msg=success");
                        return;

                    } else {
                        //if ($isCodeLengthValid && ($coupon_code == $rows1[0]['code']) && ($rows1[0]['is_active']==1) && ($rows2[0]['voucher']!=$coupon_code) && ($expiryDate>$todaysDate)) {
                        Mage::getSingleton('core/session')->addError($this->__('Voucher code "%s" is not valid.', Mage::helper('core')->escapeHtml($coupon_code)));
                        return $this->getResponse()->setRedirect($this->_getRefererUrl());
                    }
                } else {
                    Mage::getSingleton('core/session')->addSuccess($this->__('Coupon code was canceled.'));
                }


            }
            catch (Exception $e) {
                $message = $this->__('Something wrong with data');
                echo Mage::getSingleton('core/session')->addError($message);
                return;
            }

        }
        $this->_redirect('frontdeals');

    }
    public function Step1Action()
    {
        $pdata = $this->getRequest()->getParam('id');
        // echo count(Mage::getModel("deals/deals")->load($pdata));
        /* if(count(Mage::getModel("deals/deals")->load($pdata))==1){
        $this->_redirect('frontdeals');      
        return;  
        }  */

        $this->loadLayout();
        $this->renderLayout();
    }
    public function Step2Action()
    {

        $pdata     = $this->getRequest()->getParam('id');
        $post_data = $this->getRequest()->getPost();
        if ($post_data) {

            try {
                //$model = Mage::getModel("deals/deals")->load($pdata);
                $model = Mage::getModel("deals/deals")->load($pdata)->addData($post_data);

                /* print_r($model->getData());
                print_r($post_data);    */
                $model->save();
                // $insertId = $model->save()->getId();
                $message = $this->__('Success');

                echo Mage::getSingleton('core/session')->addSuccess($message);
                $this->_redirect('frontdeals');
                return;
            }
            catch (Exception $e) {
                $message = $this->__('Something wrong with data');
                echo Mage::getSingleton('core/session')->addError($message);
                return;
            }

        }
        $this->_redirect('frontdeals');
    }
    public function ThankyouAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

}