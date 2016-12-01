<?php
/**
 * Group display controller
 *
 * @category   Halox
 * @package    Halox_Groupdisplay
 * @author     Chetu Team
 */
class Halox_Groupdisplay_Adminhtml_Catalog_Product_Set_GroupController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * check controller acl
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
    
    /**
     * edit group action
     * @return null
     */
    public function editAction()
    {
        
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Attribute Sets'))
             ->_title($this->__('Manage Groups Display Settings'));

        
        $attributeSet = Mage::getModel('eav/entity_attribute_set')
            ->load($this->getRequest()->getParam('id'));
        
        
        if (!$attributeSet->getId()) {
            $this->_redirect('*/*/index');
            return;
        }

        $this->_title($attributeSet->getId() ? $attributeSet->getAttributeSetName() : $this->__('New Set'));

        Mage::register('current_attribute_set', $attributeSet);

        $this->loadLayout();
        $this->_setActiveMenu('catalog/sets');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper('catalog')->__('Catalog'), Mage::helper('catalog')->__('Catalog'));
        $this->_addBreadcrumb(
            Mage::helper('catalog')->__('Manage Product Sets'),
            Mage::helper('catalog')->__('Manage Product Sets'));

        $this->_addContent($this->getLayout()->createBlock('halox_groupdisplay/adminhtml_catalog_product_attribute_group_form_edit'));

        $this->renderLayout();
    }

    /**
     * Save attribute set action
     *
     * [POST] Create attribute set from another set and redirect to edit page
     * [AJAX] Save attribute set data
     */
    public function saveAction()
    {
        $attributeSetId = $this->getRequest()->getParam('id', false);
        
        if (!$attributeSetId) {
            $this->_redirectReferer();
            return;
        }
        
        $postData = $this->getRequest()->getPost('group');

        if ( ! isset($postData['show_on_frontend'])) {
            $this->_redirectReferer();
            return;
        }
        
        try {
            

            $mappingCollection = Mage::getModel('halox_groupdisplay/mapping')->getCollection();
            $mappingCollection->addFieldToFilter('attribute_set_id', $attributeSetId);

            /* @var $model Interactone_Attributedisplay_Model_Attribute_Set_Display */

            $insertqueryStr = "INSERT INTO halox_group_display_mapping (attribute_set_id,group_id,show_on_frontend) VALUES";
            $updateQuery ="UPDATE halox_group_display_mapping SET ";
            $updAttrStr ="attribute_set_id= ( CASE ";
            $groupIdStr =", group_id = ( CASE ";
            $showOnFrontEnd = ", show_on_frontend = ( CASE ";
            $insertquery = array();
            foreach($postData['show_on_frontend'] as $groupId => $groupData){
                
                $showOnFrontend = $groupData['value'];
                if(isset($groupData['id'])){
                    $id = $groupData['id'];
                    $updAttrStr.=" WHEN id = $id THEN $attributeSetId";
                    $groupIdStr .= " WHEN  id= $id THEN $groupId";
                    $showOnFrontEnd .= " WHEN id= $id THEN $showOnFrontend";
                }else{
                    $insertquery[]= "($attributeSetId, $groupId,  $showOnFrontend)";
                }
               
            }
            
            
            if(isset($updAttrStr)){
               $updAttrStr.=" ELSE attribute_set_id END)";
             }
            if(isset($groupIdStr)){
               $groupIdStr.=" ELSE group_id END )";
             }
             
             if(isset($showOnFrontEnd)){
               $showOnFrontEnd.=" ELSE show_on_frontend END )";
             }
             
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
             if(isset($updAttrStr,$groupIdStr,$showOnFrontEnd)){
               $updateQuery = $updateQuery.$updAttrStr.$groupIdStr.$showOnFrontEnd;
               $writeConnection->query($updateQuery);
             }
             
           if(isset($insertquery) && !empty($insertquery)){
               $insertStr = implode(',',$insertquery);
               $insertqueryStr .= $insertStr;
               $writeConnection->query($insertqueryStr);
           }
        
            $this->_getSession()->addSuccess(Mage::helper('halox_groupdisplay')->__('Attribute group display settings have been saved.'));
            
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            
        } catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('interactone_attributedisplay')->__('An error occurred while saving the attribute set display.'));
            
        }

        $this->_redirect('*/catalog_product_set/', array('_current'=>true));;
    }
   
}