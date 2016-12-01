<?php
/**
 * handles all the AJAX actions for the bulk order grid
 */
class Halox_BulkOrder_GridController extends Mage_Core_Controller_Front_Action
{
	
	/**
	 * @return helper instance
	 */
	protected function _getHelper()
	{
		return Mage::helper('halox_bulkorder');
	}

	/**
	 * @return cart instance singleton
	 */
	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}

	/**
	 * get checkout session
	 * @return checkout session instance
	 */
	protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
	 * return locale filter to correctly filter out qty
	 */
	protected function _getLocalFilter()
	{
		return new Zend_Filter_LocalizedToNormalized(
            array('locale' => Mage::app()->getLocale()->getLocaleCode())
        );
	}

	/**
	 * load parent configurable product bind to current vertical tab
	 * @return false || product instance
	 */
	protected function _initParent()
	{
		$parentId = (int) $this->getRequest()->getPost('vertical_active_id');
        
        $parentProduct = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($parentId);
            

        if( ! $parentProduct){
			$this->getResponse()->setHeader('HTTP/1.0', '500', true);
			$this->getResponse()->setBody('Parent product not found in the store.');
			return;
		}

		return $parentProduct;
	}

	/**
	 * redirect user to login page if customer session has expired
	 */
	public function preDispatch()
	{
		parent::preDispatch();

		if(Mage::app()->getWebsite()->getCode() == 'halo_wholesale'
			&& $this->getRequest()->isXmlHttpRequest()
			&& ! Mage::getSingleton('customer/session')->isLoggedIn()

		){
			$this->setFlag('',self::FLAG_NO_DISPATCH,true);
            
            $responseData = new Varien_Object(array(
            	'status' => 'ERROR',
            	'message' => 'Session has expired unexpectedly. Please wait while we redirect you to login screen.',
            	'location' => Mage::helper('customer')->getLoginUrl(),
            	'read_only' => true
            ));
            

            $this->getResponse()
			    ->clearHeaders()
			    ->setHeader('HTTP/1.0', 401, true)
			    ->setHeader('Content-Type', 'text/json')
			    ->setBody(Mage::helper('core')->jsonEncode($responseData));
			
			return;
		}
	}

	/**
	 * validate post data for errors and set response accordingly
	 */
	protected function _validatePostData($postData)
	{
		if( ! is_array($postData)){
			$this->getResponse()->setHeader('HTTP/1.0', '400', true)
				->setBody('Invalid parameters provided.')
				->sendResponse();
			exit;
		}

		if( ! isset($postData['isAjax']) || ! $postData['isAjax']){
			
			$this->getResponse()->setHeader('HTTP/1.0', '400', true)
				->setBody('Invalid parameters provided.')
				->sendResponse();;
			exit;
		}

		if( ! isset($postData['base_category_id']) || ! $postData['base_category_id']){
			$this->getResponse()->setHeader('HTTP/1.0', '400', true)
				->setBody('Invalid parameters provided.')
				->sendResponse();
			exit;
		}

		if( ! isset($postData['horizontal_active_id']) || ! $postData['horizontal_active_id']){
			$this->getResponse()->setHeader('HTTP/1.0', '400', true)
				->setBody('Invalid parameters provided.')
				->sendResponse();
			exit;
		}

		if( ! isset($postData['vertical_active_id']) || ! $postData['vertical_active_id']){
			$this->getResponse()->setHeader('HTTP/1.0', '400', true)
				->setBody('Invalid parameters provided.')
				->sendResponse();
			exit;
		}

		if( ! isset($postData['super_attribute']) || ! is_array($postData['super_attribute']) || empty($postData['super_attribute'])){
			$this->getResponse()->setHeader('HTTP/1.0', '400', true)
				->setBody('Invalid parameters provided.')
				->sendResponse();
			exit;
		}

		$parentId = $postData['vertical_active_id'];
		if( ! isset($postData['super_attribute'][$parentId]) || ! is_array($postData['super_attribute'][$parentId]) || empty($postData['super_attribute'][$parentId])){
			$this->getResponse()->setHeader('HTTP/1.0', '400', true)
				->setBody('Invalid parameters provided.')
				->sendResponse();
			exit;
		}		
	}

	/**
     * check item qty > 0 otherwise unset item from post data array 
	 */
	protected function _validateQty(&$postData, $parentProduct)
	{	
		foreach($postData as $key => &$postItem){
			
			if( $key != 'super_attribute'){
				continue;
			}

			foreach($postItem[$parentProduct->getId()] as $optionId => &$option){

				if( ! isset($option['qty']) 
					|| ! is_numeric($option['qty']) 
					|| $option['qty'] <= 0
				){
					unset($postItem[$parentProduct->getId()][$optionId]);
				}	
			}

		}
    }

	/**
	 * prepare cart params as per the cell product super attributes
 	 */
	protected function _prepareCartParams($productConfig)
	{
		$attrOptions = $productConfig;
		
		if(isset($attrOptions['quote_item'])){
			unset($attrOptions['quote_item']);
		}

		if(isset($attrOptions['halox_bulk_buyRequest'])){
			unset($attrOptions['halox_bulk_buyRequest']);
		}

		unset($attrOptions['stock']);
		unset($attrOptions['qty']);

		$buyRequest['super_attribute'] = $attrOptions;
		$buyRequest['qty'] = $this->_getLocalFilter()->filter($productConfig['qty']);

		if(isset($productConfig['halox_bulk_buyRequest'])){
			$buyRequest['halox_bulk_buyRequest'] = $productConfig['halox_bulk_buyRequest'];
		}

		return $buyRequest;
	}

	/**
	 * generate html needed to update sections of current page
	 */
	protected function _loadLayoutToResponse(&$response)
    {

    	$this->loadLayout();

    	$toplink = "";
        if($this->getLayout()->getBlock('minicart')){
			$toplink = $this->getLayout()->getBlock('minicart')->toHtml();
        }

        $cartSidebar = "";
		if($this->getLayout()->getBlock('cart_sidebar')){
            $cartSidebar = $this->getLayout()->getBlock('cart_sidebar')->toHtml();
        }

        Mage::register('referrer_url', $this->_getRefererUrl());

        $response->setToplink($toplink);
        $response->setCartSidebar($cartSidebar);

        return $this;

    }

	/**
	 * add product to cart with given qty and super attributes
	 */
	protected function _addProductToCart($product, $productConfig)
	{
		$cartParams = $this->_prepareCartParams($productConfig);

		//passing product instance somehow doesn't work
		$this->_getCart()->addProduct($product, $cartParams);
	}

	/**
	 * update quote item qtys and item options correctly back to new cart item.
	 */
	protected function _updateProductToCart($product, $productConfig)
	{
		$quoteItemId = (int) $productConfig['quote_item'];

		$quoteItem = $this->_getCart()->getQuote()->getItemById($quoteItemId);

		if (!$quoteItem) {
            Mage::throwException($this->__('Quote item not found.'));
        }

        $cartParams = $this->_prepareCartParams($productConfig);

        $item = $this->_getCart()->updateItem($quoteItemId, $cartParams);

        if (is_string($item)) {
            Mage::throwException($item);
        }
        if ($item->getHasError()) {
            Mage::throwException($item->getMessage());
        }
        
        Mage::dispatchEvent('checkout_cart_update_item_complete',
            array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
        );

    }

	/**
	 * delete items which were added originally but removed from cart later
	 */
	protected function _deleteItemFromCart($quoteItemId)
	{
		$this->_getCart()->getQuote()->removeItem($quoteItemId);

		return $this;
	}

	/**
	 * load layout handles based on the tab clicked on frontend to
	 * send back as AJAX response
	 */
	protected function _getTabsHtml($tabType)
	{
		$layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('bulkorder_grid_tab_' . $tabType);
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
	}

	/**
	 * render grid tab content based on the tab type and the entity associated with it
	 * @param $tabType vertical/horizontal
	 * @param $id entity id 
	 */
	public function renderTabContentAction()
	{
		$response = new Varien_Object;

		$entityId = Mage::app()->getRequest()->getParam('id');
		if( ! $entityId){
			
			$this->getResponse()->setHeader('HTTP/1.0', '400', true);
			$this->getResponse()->setBody('Invalid parameters provided.');

		}

		$baseCategoryId = Mage::app()->getRequest()->getParam('base_id');
		if( ! $baseCategoryId){
			
			$this->getResponse()->setHeader('HTTP/1.0', '400', true);
			$this->getResponse()->setBody('Invalid parameters provided.');

		}

		$tabType = Mage::app()->getRequest()->getParam('type');
		if( ! $tabType){
			
			$this->getResponse()->setHeader('HTTP/1.0', '400', true);
			$this->getResponse()->setBody('Invalid parameters provided.');

		}

		$parentEntityId = Mage::app()->getRequest()->getParam('parent');
		if( ! $parentEntityId && ($tabType == Halox_BulkOrder_Helper_Data::GRID_TAB_TYPE_VERTICAL)){
			
			$this->getResponse()->setHeader('HTTP/1.0', '400', true);
			$this->getResponse()->setBody('Invalid parameters provided.');			

		}

		$isMultiStep = Mage::app()->getRequest()->getParam('is_multi_step', '');

		Mage::register('current_base_category_id', $baseCategoryId);
		Mage::register('current_entity_id', $entityId);
		Mage::register('current_tab_type', $tabType);
		
		if($isMultiStep){
			Mage::register('is_multi_step', $isMultiStep);	
		}
		

		if($parentEntityId){
			Mage::register('parent_entity_id', $parentEntityId);
		}

		$tabHtml = $this->_getTabsHtml($tabType);

		$this->getResponse()->setHeader('HTTP/1.0', '200', true);
		$this->getResponse()->setBody($tabHtml);

	}

	/**
	 * add mulitple configurable products to cart
	 */
	public function addtocartAction()
	{
		$response = new Varien_Object;
		
		$postData =	$this->getRequest()->getPost();

		$this->_validatePostData($postData);

		$parentProduct = $this->_initParent();

		$this->_validateQty($postData, $parentProduct);

		try{

			$superAttributes = $postData['super_attribute'][$parentProduct->getId()];
            
            foreach($superAttributes as $simpleProdId => $simpleProdConfig){
			
				//register for which item exception appeared
				$response->setParentProdId($parentProduct->getId());
				$response->setSimpleProdId($simpleProdId);
				$response->setSimpleProdConfig($simpleProdConfig);
					
				//$parentProduct->addCustomOption('halox_bulk_buyRequest', serialize($postData));
				
				$simpleProdConfig['halox_bulk_buyRequest'] = serialize($postData);

				$this->_addProductToCart(clone $parentProduct, $simpleProdConfig);

			}

			$this->_getCart()->save();

			$this->_getSession()->setCartWasUpdated(true);	

			/**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array(
                	'product' => $parentProduct, 
                	'request' => $this->getRequest(), 
                	'response' => $this->getResponse()
                )
            );

            if ( ! $this->_getSession()->getNoCartRedirect(true)) {
                
                if ( ! $this->_getCart()->getQuote()->getHasError()) {
                    
                    $message = $this->__('These products have been added to the cart.');
                    
                    $response->setMessage($message);
                    $response->setStatus('SUCCESS');
                    $response->setResetGrid(1);

                    $this->_loadLayoutToResponse($response);

				}else{

					$message = $this->__('Couldn\'t add these products to the cart.');

					$response->setMessage($message);
                    $response->setStatus('ERROR');

                }

            }		

		}catch (Mage_Core_Exception $e) {
            
            if ($this->_getSession()->getUseNotice(true)) {
				$response->setMessage(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $response->setMessage($e->getMessage());
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $response->setRedirectUrl($url);
            }

            $response->setStatus('ERROR');

		}catch (Exception $e) {
            
            Mage::logException($e);

            $response->setMessage($this->__('Cannot add the item to shopping cart.'));
            $response->setStatus('ERROR');
            
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		
	}

	
	/**
	 * update bulk order grid cart items with new qtys from E-Liquid form
	 */
	public function updatecartAction()
	{
		$response = new Varien_Object;
		
		$postData =	$this->getRequest()->getPost();

		$this->_validatePostData($postData);

		$parentProduct = $this->_initParent();

		try{

			$filter = new Zend_Filter_LocalizedToNormalized(
                array('locale' => Mage::app()->getLocale()->getLocaleCode())
            );

            $superAttributes = $postData['super_attribute'][$parentProduct->getId()];

            foreach($superAttributes as $simpleProdId => $simpleProdConfig){

				//register for which item exception appeared
				$response->setParentProdId($parentProduct->getId());
				$response->setSimpleProdId($simpleProdId);
				$response->setSimpleProdConfig($simpleProdConfig);

				if(isset($simpleProdConfig['is_delete']) && $simpleProdConfig['is_delete']){
					$this->_deleteItemFromCart($simpleProdConfig['quote_item']);
					continue; 
				}

				//$parentProduct->addCustomOption('halox_bulk_buyRequest', serialize($postData));
				
				$simpleProdConfig['halox_bulk_buyRequest'] = serialize($postData);	
				
				if(isset($simpleProdConfig['quote_item']) && $simpleProdConfig['quote_item']){
					$this->_updateProductToCart(clone $parentProduct, $simpleProdConfig);
				}else{
					$this->_addProductToCart(clone $parentProduct, $simpleProdConfig);	
				}
			}

			$this->_getCart()->save();

	        $this->_getSession()->setCartWasUpdated(true);

	        if (!$this->_getSession()->getNoCartRedirect(true)) {
	            if (!$this->_getCart()->getQuote()->getHasError()) {
	                $message = $this->__('Your cart has been updated. Please wait while we redirect you to the cart...');
	                
	                $response->setMessage($message);
	                $response->setStatus('SUCCESS');
	                $response->setReadOnly(1);

	                $response->setRedirectUrl(Mage::getUrl('checkout/cart'));

	                $this->_loadLayoutToResponse($response);

	            }else{

	            	$message = $this->__('Couldn\'t add these products to your cart.');

					$response->setMessage($message);
                    $response->setStatus('ERROR');
	            }
	            
	        }

		}catch (Mage_Core_Exception $e) {
            
            if ($this->_getSession()->getUseNotice(true)) {
				$response->setMessage(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $response->setMessage($e->getMessage());
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $response->setRedirectUrl($url);
            }

            $response->setStatus('ERROR');

		}catch (Exception $e) {
            
            Mage::logException($e);

            $response->setMessage($this->__('Couldn\'t update shopping cart.'));
            $response->setStatus('ERROR');
            
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}

}