<?php
/**
 * Pickup
 *
 * @package      :  Paypal_Pickup
 * @version      :  0.1.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
class Paypal_Pickup_Block_Adminhtml_Place_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('placeGrid');
        $this->setDefaultSort('place_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('paypal_pickup/place')->getCollection()
            ->addFieldToFilter('is_deleted', 0);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('place_id', array(
            'header' => Mage::helper('paypal_pickup')->__('ID'),
            'align' => 'right',
            'index' => 'place_id',
        ));

        $this->addColumn('place_code', array(
            'header' => Mage::helper('paypal_pickup')->__('Place Code'),
            'align' => 'left',
            'index' => 'place_code',
        ));

        $this->addColumn('place_name', array(
            'header' => Mage::helper('paypal_pickup')->__('Place Name'),
            'align' => 'left',
            'index' => 'place_name',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('paypal_pickup')->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                0 => 'Disabled',
            ),
        ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('paypal_pickup')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('paypal_pickup')->__('Edit'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getPlaceId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('place_id');
        $this->getMassactionBlock()->setFormFieldName('place');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('paypal_pickup')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('paypal_pickup')->__('Are you sure to delete this place?')
        ));

        return $this;
    }

}