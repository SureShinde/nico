<?php

class Halox_AgeVerification_Block_Adminhtml_Ageverification_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('ageverificationGrid');
        $this->setDefaultSort('ageverification_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('ageverification/ageverification')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('ageverification_id', array(
            'header' => Mage::helper('ageverification')->__('ID'),
            'align' => 'center',
            'width' => '50px',
            'index' => 'ageverification_id',
        ));

        $this->addColumn('country', array(
            'header' => Mage::helper('ageverification')->__('Country'),
            'align' => 'center',
            'index' => 'country',
            'type' => 'options',
            'options' => Mage::helper('ageverification')->getCountryCollection(),
        ));

        $this->addColumn('state', array(
            'header' => Mage::helper('ageverification')->__('State'),
            'align' => 'center',
            'index' => 'state',
            'type' => 'options',
            'options' => Mage::helper('ageverification')->getStateCollection(),
        ));

        $this->addColumn('pincode', array(
            'header' => Mage::helper('ageverification')->__('Postal Code'),
            'align' => 'center',
            'index' => 'pincode',
        ));

        $this->addColumn('age', array(
            'header' => Mage::helper('ageverification')->__('Minimum Age'),
            'align' => 'center',
            'index' => 'age',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('ageverification')->__('Status'),
            'align' => 'center',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('ageverification')->__('Action'),
            'width' => '100',
            'align' => 'center',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('ageverification')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('ageverification')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('ageverification')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('ageverification_id');
        $this->getMassactionBlock()->setFormFieldName('ageverification');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('ageverification')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('ageverification')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('ageverification/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('ageverification')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('ageverification')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
