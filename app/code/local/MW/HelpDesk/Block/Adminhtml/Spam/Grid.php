<?php
class MW_HelpDesk_Block_Adminhtml_Spam_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct($attributes = array()) {
        parent::__construct();
        $this->setId('spamGrid');
        $this->setUseAjax(true);
        $this->setDefaultDir('DESC');
    }
    
     protected function _prepareCollection() {
         
         $collection = Mage::getModel('helpdesk/spam')->getCollection();
         $this->setCollection($collection);
         
         return parent::_prepareCollection();
     }
     
     protected function _prepareColumns() {
          $this->addColumn('id', array(
              'header'  => Mage::helper('helpdesk')->__('ID'),
              'align' => 'left',
              'index' => 'id',
          ));
          $this->addColumn('email', array(
              'header'  => Mage::helper('helpdesk')->__('Email (Mark email as Spam under Manage Ticket - Detail)'),
              'align' => 'left',
              'index' => 'email',
          ));
         return parent::_prepareColumns();
     }
     
     protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

         $this->getMassactionBlock()->addItem('delete', array(
              'label'    => Mage::helper('helpdesk')->__('Delete'),
              'url'      => $this->getUrl('*/*/massDelete'),
              'confirm'  => Mage::helper('helpdesk')->__('Are you sure to delete?')
         ));
    }
}
?>
