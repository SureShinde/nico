<?php

require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'CartController.php';

class Halox_WholesaleCartUpdate_CartController extends Mage_Checkout_CartController 
{

	/**
     * Delete shoping cart item action for bundle child items
     */
    public function bundleItemDeleteAction()
    {
        
        if ($this->_validateFormKey()) {
            $id = (int)$this->getRequest()->getParam('id');
            if ($id) {
                try {
                    
                    $item = Mage::getModel('sales/quote_item')->load($id);

                    if ($item) {
                        
                        $this->_deleteBundleItemFromQuote($item);

                    }
                    
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('Cannot remove the item.'));
                    Mage::logException($e);
                }
            }
        } else {
            $this->_getSession()->addError($this->__('Cannot remove the item.'));
        }

        $this->_redirectReferer(Mage::getUrl('checkout/cart/index'));
    }


	/**
     * Update shopping cart data action
     */
    public function updatePostAction()
    {
        
    	if (!$this->_validateFormKey()) {
            $this->_redirect('checkout/cart/index');
            return;
        }

        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }

        $this->_goBack();
    }

    /**
     * Update customer's shopping cart
     */
    protected function _updateShoppingCart()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                
                $this->_updateItems($cart, $cartData);
                    
                $cart->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }

    protected function _deleteBundleItemFromQuote($item, $parentItem = null)
    {

    	$parentItemId = $parentItem ? $parentItem->getId() : $item->getParentItemId();

        //delete child bundle item and if only one bundle item is there
		//to delete then delete the parent item as well.
		$childItemCollection = Mage::getModel('sales/quote_item')->getCollection()
            ->setQuote($this->_getSession()->getQuote())
            ->addFieldToFilter('parent_item_id', array(
                'eq' => $parentItemId
            )
        );    

        $childItemCount = $childItemCollection->getSize();

        $this->_getSession()->getQuote()->setIsMultiShipping(false);

        if($childItemCount == 1){
            
            if(!$parentItem){
                $parentItem = Mage::getModel('sales/quote_item')->getCollection()
                        ->setQuote($this->_getSession()->getQuote())
                        ->addFieldToFilter('item_id', array('eq' => $parentItemId))
                        ->setPageSize(1)
                        ->getFirstItem();
            }

            Mage::dispatchEvent('sales_quote_remove_item', array(
                'parent_quote_item' => $parentItem,
                'quote_item' => $item
                
            ));        

            $parentItem->delete();
            
        }else{
            
            Mage::dispatchEvent('sales_quote_remove_item', array(
                'quote_item' => $item,
                
            ));

            $item->delete();
        }

        

    }

    /**
     * Update cart items information
     *
     * @param   array $data
     * @return  Mage_Checkout_Model_Cart
     */
    public function _updateItems($cart, $data)
    {
        Mage::dispatchEvent('checkout_cart_update_items_before', array('cart'=>$cart, 'info'=>$data));

        /* @var $messageFactory Mage_Core_Model_Message */
        $messageFactory = Mage::getSingleton('core/message');
        $session = $this->_getSession();
        $qtyRecalculatedFlag = false;
        foreach ($data as $itemId => $itemInfo) {
            $item = $this->_getSession()->getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }

            if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && $itemInfo['qty']=='0')) {
                
            	//if trying to delete bundle child item then handle this situation differently
            	if($item->getParentItemId()){
            		
            		$parentItem = Mage::getModel('sales/quote_item')->getCollection()
            			->setQuote($item->getQuote())
            			->addFieldToFilter('item_id', array('eq' => $item->getParentItemId()))
            			->setPageSize(1)
            			->getFirstItem();
            		
            		if($parentItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
            			$this->_deleteBundleItemFromQuote($item, $parentItem);	
            		}
            		
				}else{

					$cart->removeItem($itemId);
            	}

                continue;
            }

            $qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
            if ($qty > 0) {
                $item->setQty($qty);

                $itemInQuote = $this->_getSession()->getQuote()->getItemById($item->getId());

                if (!$itemInQuote && $item->getHasError()) {
                    Mage::throwException($item->getMessage());
                }

                if (isset($itemInfo['before_suggest_qty']) && ($itemInfo['before_suggest_qty'] != $qty)) {
                    $qtyRecalculatedFlag = true;
                    $message = $messageFactory->notice(Mage::helper('checkout')->__('Quantity was recalculated from %d to %d', $itemInfo['before_suggest_qty'], $qty));
                    $session->addQuoteItemMessage($item->getId(), $message);
                }
            }
        }

        if ($qtyRecalculatedFlag) {
            $session->addNotice(
                Mage::helper('checkout')->__('Some products quantities were recalculated because of quantity increment mismatch')
            );
        }

        Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$cart, 'info'=>$data));
        return $this;
    }

    /**
     * Update product configuration for a cart item
     */
    public function updateItemOptionsAction()
    {
        $cart   = $this->_getCart();
        
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        $response = new Varien_Object;
        if(!isset($params['isAjax']) && $params['isAjax'] != 1){
            $this->getResponse()->setHeader('HTTP/1.0', '400', true)
                ->setBody('<div class="msg">Invalid parameters provided.</div>')
                ->sendResponse();
            exit;
        }

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('<div class="msg">Quote item is not found.</div>'));
            }

            /**
             * Magento is not able to handle children properly when updating items 
             * via AJAX.
             * So we removed the original parent item and replaced it with a clone
             * which has $_children set to empty array
             */
            $cloneItem = clone $quoteItem;
            $cloneItem->setId($quoteItem->getId());
            
            $cart->getQuote()->removeItem($quoteItem->getId());
            $cart->getQuote()->addItem($cloneItem);    
            
            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }
            
            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);
            
            Mage::dispatchEvent('checkout_cart_update_item_complete',
                array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    
                    $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->escapeHtml($item->getProduct()->getName()));
                    
                    $response->setMessage('<div class="msg">' . $message . '</div>');
                    $response->setQuoteItemId($item->getId());
                    
                    $this->_loadLayoutToResponse($response);

                    $this->getResponse()
                        ->setHeader('HTTP/1.0', '200', true)
                        ->setHeader('Content-Type', 'application/json')
                        ->setBody(Mage::helper('core')->jsonEncode($response))
                        ->sendResponse();

                    exit;

                }
                
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                
                $this->getResponse()->setHeader('HTTP/1.0', '500', true)
                    ->setBody('<div class="msg">' . $e->getMessage() . '</div>')
                    ->sendResponse();
                exit;

            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                $responseMsg = '';
                foreach ($messages as $message) {
                    $responseMsg .= $message . '<br>'; 
                }

                rtrim($responseMsg, '<br>');

                $this->getResponse()->setHeader('HTTP/1.0', '500', true)
                    ->setBody('<div class="msg">' . $responseMsg . '</div>')    
                    ->sendResponse();
                exit;
            }

            
        } catch (Exception $e) {
            Mage::logException($e);
            
            $message = $this->__('Cannot update the item.');

            $this->getResponse()->setHeader('HTTP/1.0', '500', true)
                ->setBody('<div class="msg">' . $message . '</div>')
                ->sendResponse();
            exit;

        }
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
}