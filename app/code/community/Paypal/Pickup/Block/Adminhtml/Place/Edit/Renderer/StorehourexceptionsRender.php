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
 * @Creation Date:  12/03/2013
 *
 */
class Paypal_Pickup_Block_Adminhtml_Place_Edit_Renderer_StorehourexceptionsRender
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $magentoAttributes;

    public function __construct()
    {
        $this->addColumn('day', array(
            'label' => Mage::helper('paypal_pickup')->__('Date'),
            'style' => 'width:150px',
            'element_style' => 'width:80px',
            'render_type' => 'datetime',
            'class' => 'input-text required-entry'
        ));

        $this->addColumn('is_open', array(
            'label' => Mage::helper('adminhtml')->__('Open'),
            'style' => 'width:150px',
            'render_type' => 'radio'
        ));

        $this->addColumn('from_hour', array(
            'label' => Mage::helper('adminhtml')->__('From Hour'),
            'style' => 'width:50px; border-right:none;',
            'element_style' => 'width:50px',
            'render_type' => 'select',
            'depend_is_open' => true,
            'values' => Mage::helper('paypal_pickup')->getHourOptionArray(),
            'class' => 'select ex-select-datetime from-hour'
        ));

        $this->addColumn('from_minute', array(
            'label' => Mage::helper('adminhtml')->__('From Minute'),
            'style' => 'width:50px; border-right:none;',
            'element_style' => 'width:50px',
            'render_type' => 'select',
            'depend_is_open' => true,
            'values' => Mage::helper('paypal_pickup')->getMinuteOptionArray(),
            'class' => 'select ex-select-datetime from-minute'
        ));

        $this->addColumn('to_hour', array(
            'label' => Mage::helper('adminhtml')->__('To Hour'),
            'style' => 'width:50px; border-right:none;',
            'element_style' => 'width:50px',
            'render_type' => 'select',
            'depend_is_open' => true,
            'values' => Mage::helper('paypal_pickup')->getHourOptionArray(),
            'class' => 'select ex-select-datetime to-hour'
        ));

        $this->addColumn('to_minute', array(
            'label' => Mage::helper('adminhtml')->__('To Minute'),
            'style' => 'width:50px',
            'element_style' => 'width:50px',
            'render_type' => 'select',
            'depend_is_open' => true,
            'values' => Mage::helper('paypal_pickup')->getMinuteOptionArray(),
            'class' => 'select ex-select-datetime to-minute'
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Exception');

        parent::__construct();
        $this->setTemplate('paypal/pickup/place/edit/renderer/store_hour_exceptions.phtml');
    }

    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception(Mage::helper('paypal_pickup')->__('Wrong column name specified.'));
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
        $idName = '#{_id}' . '_' . $columnName;
        $groupDependByIsOpen = '';
        if ($column['depend_is_open']) {
            $groupDependByIsOpen = $idName = '#{_id}' . '_is_open';
        }

        $type = $column['render_type'];
        $rendered = '';
        if ($type == 'datetime') {
            $rendered = '<div class="' . $groupDependByIsOpen . '">';
            $rendered .= '<input class="' . $column['class'] . '" style="' . $column['element_style'] . '" type="text" name="' . $inputName . '" id="' . $idName . '" value="" />';
            $rendered .= '<img class="calendar-trig" title="' . Mage::helper('paypal_pickup')->__('Select Date') . '" id="' . $idName . '_trig" src="' . $this->getSkinUrl('images/grid-cal.gif') . '" class="v-middle" />';
            $rendered .= '</div>';
        } elseif ($type == 'select') {
            $colLabel = '';
            $colSeparator = '';
            $rendered = '<div class="' . $groupDependByIsOpen . '">';
            if ($columnName == 'from_hour') {
                $colLabel = '<label class="label-first">' . Mage::helper('paypal_pickup')->__('From') . '</label>';
                $colSeparator = '<label class="label-last">:</label>';
            } elseif ($columnName == 'to_hour') {
                $colLabel = '<label class="label-first">' . Mage::helper('paypal_pickup')->__('To') . '</label>';
                $colSeparator = '<label class="label-last">:</label>';
            }
            $rendered .= $colLabel;
            $rendered .= '<select class="' . $column['class'] . '" style="' . $column['element_style'] . '" name="' . $inputName . '" id="' . $idName . '" onchange="datetimeOnChange(this)">';
            $values = $column['values'];
            foreach ($values as $value) {
                $rendered .= '<option value="' . $value['value'] . '">' . $value['label'] . '</option>';
            }
            $rendered .= '</select>';
            $rendered .= $colSeparator;
            if ($columnName == 'to_minute') {
                $rendered .= '<div class="validation-advice ex-validate-datetime" style="display: none">' . Mage::helper('paypal_pickup')->__('To time must be greater than From time.') . '</div>';
            }
            $rendered .= '</div>';
        } elseif ($type == 'radio') {
            if ($columnName == 'is_open') {
                $rendered = '<div class="' . $groupDependByIsOpen . '">';
                $rendered .= '<input rel="' . $idName . '" onchange="isOpenOnChange(this)" type="radio" style="' . $column['element_style'] . '" name="' . $inputName . '" id="' . $idName . '1" value="1" checked="checked" class="radio-is-open"/>';
                $rendered .= '<label class="label-radio">' . Mage::helper('paypal_pickup')->__('Open') . '</label>';
                $rendered .= '<input rel="' . $idName . '" onchange="isOpenOnChange(this)" type="radio" style="' . $column['element_style'] . '" name="' . $inputName . '" id="' . $idName . '0" value="0" />';
                $rendered .= '<label class="label-radio">' . Mage::helper('paypal_pickup')->__('Close') . '</label>';
                $rendered .= '</div>';
            }
        }
        return $rendered;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getElementHtml($element);
        return $html;
    }

    public function getCurrentGroupOption()
    {
        $groupOption = Mage::registry('group_option_data');
        return $groupOption;
    }


    public function addColumn($name, $params)
    {
        $this->_columns[$name] = array(
            'label' => empty($params['label']) ? 'Column' : $params['label'],
            'size' => empty($params['size']) ? false : $params['size'],
            'style' => empty($params['style']) ? null : $params['style'],
            'element_style' => empty($params['element_style']) ? null : $params['element_style'],
            'class' => empty($params['class']) ? null : $params['class'],
            'values' => empty($params['values']) ? null : $params['values'],
            'render_type' => empty($params['render_type']) ? null : $params['render_type'],
            'depend_is_open' => empty($params['depend_is_open']) ? false : $params['depend_is_open'],
            'renderer' => false,
        );
        if ((!empty($params['renderer'])) && ($params['renderer'] instanceof Mage_Core_Block_Abstract)) {
            $this->_columns[$name]['renderer'] = $params['renderer'];
        }
    }
}

?>