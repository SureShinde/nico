<?php

class Sunovisio_CustomerFlag_Block_Adminhtml_Customerflag_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('customerflagGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('customerflag/flag')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('customerflag')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
        ));
        
        $this->addColumn('code', array(
            'header' => Mage::helper('customerflag')->__('Code'),
            'align' => 'left',
            'index' => 'code',
        ));
        
        $this->addColumn('label', array(
            'header' => Mage::helper('customerflag')->__('Label'),
            'align' => 'left',
            'index' => 'label'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('customerflag')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                0 => 'Disabled',
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('customerflag')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('customerflag')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        //$this->addExportType('*/*/exportCsv', Mage::helper('customerflag')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('customerflag')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('customerflag');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('customerflag')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('customerflag')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('customerflag/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('customerflag', array(
            'label' => Mage::helper('customerflag')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('customerflag')->__('Status'),
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