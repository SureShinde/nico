<?php
/**
 * Group display settings edit form
 *
 * @category   Halox
 * @package    Halox_Groupdisplay
 * @author     Chetu Team
 */

class Halox_Groupdisplay_Block_Adminhtml_Catalog_Product_Attribute_Group_Form_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     *  get the mapping data by the current attribute set id
     */
    protected function _getMappingDataBySet(){
        
        $mapCollection = Mage::getModel('halox_groupdisplay/mapping')->getCollection()
            ->addFieldToFilter('attribute_set_id', $this->getRequest()->getParam('id'));

        $visibleGroupsData = array();
        
        foreach($mapCollection as $item){
            $visibleGroupsData[$item->getData('group_id')] = array(
                'mapping_id' => $item->getData('id'),
                'show_on_frontend' => $item->getData('show_on_frontend')
            );
        }    


         return $visibleGroupsData;   
    }
    
    /**
     * it prepares the form for save group settings
     * @return form
     */
    protected function _prepareForm() {
        
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

      
        $form->setUseContainer(true);
        $this->setForm($form);
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->addFieldset('edit_group', array('class' => 'group'));
        
        $groups = $this->getGroupTree(); 
        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        
        $mappingData = $this->_getMappingDataBySet();

        foreach($groups as $index => $groupArr){
            
            $groupId = $groupArr['attribute_group_id'];
            $attributeSetId = $groupArr['attribute_set_id'];
            $groupLabel = $groupArr['attribute_group_name'];

            $selected = 0;
            if(isset($mappingData[$groupId])){
                
                $selected = $mappingData[$groupId]['show_on_frontend'];
                $selectedMappingId = $mappingData[$groupId]['mapping_id'];

                $fieldset->addField("group_hidden_$groupId", 'hidden', array(
                    "name" => "group[show_on_frontend][$groupId][id]",
                    'value' => isset($selectedMappingId) ? $selectedMappingId : ''
                ));
            }
           
            
            $fieldset->addField("group_$groupId", 'select', array(
                "name" => "group[show_on_frontend][$groupId][value]",
                'label' => Mage::helper('catalog')->__("$groupLabel"),
                'values' => $yesnoSource,
                'value' => $selected
            )); 
            
        }
        
        
        return parent::_prepareForm();
    }
    
    
    /**
     * it returns the all groups of an attribute set
     * @return array of groups
     */
    public function getGroupTree()
    {
       $setId = $this->getRequest()->getParam('id');
      
        $group_list = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load()
            ->getData();
        
        return $group_list;
    }

}
