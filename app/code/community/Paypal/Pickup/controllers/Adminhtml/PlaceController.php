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
class Paypal_Pickup_Adminhtml_PlaceController extends Mage_Adminhtml_Controller_action
{

    /**
     * Init action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('place/index')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Places'), Mage::helper('adminhtml')->__('Manage Places'));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('paypal_pickup')->__('Manage Places'));
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Edit action
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (empty($id)) {
            $model = Mage::getModel('paypal_pickup/place');
            $placeTitle = Mage::helper('paypal_pickup')->__('New Place');
        } else {
            $model = Mage::getModel('paypal_pickup/place')->load($id);
            $placeTitle = Mage::helper('paypal_pickup')->__('Edit Place');
        }
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if ($model->getPlaceId() || empty($id)) {
            if (!empty($data)) {
                $model->setData($data);
            }
            if (Mage::registry('place_data'))
                Mage::unregister('place_data');
            Mage::register('place_data', $model);

            $this->_title(Mage::helper('paypal_pickup')->__('Manage Places'))->_title($placeTitle);
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('paypal_pickup/adminhtml_place_edit'))
                ->_addLeft($this->getLayout()->createBlock('paypal_pickup/adminhtml_place_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('Place does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * New action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $placeId = $this->getRequest()->getParam('id');
            $placeCode = $this->getRequest()->getParam('place_code');

            if (!empty($placeCode)) {
                $data['place_code'] = $placeCode;
            }

            // Validate min-max days
            if (!empty($data['datetime_min_days']) && !empty($data['datetime_max_days'])
                && ((int)$data['datetime_min_days'] > (int)$data['datetime_max_days'])
            ) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('Min Days must not be greater than Max Days.'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                if ($placeId) {
                    $this->_redirect('*/*/edit', array('id' => $placeId));
                } else {
                    $this->_redirect('*/*/new');
                }
                return;
            }

            // Serialize open_datetime before add data
            if ($dateTimeData = $data['open_datetime']) {
                foreach ($data['open_datetime'] as $key => $value) {
                    $fromHour = (int)$value['from_hour'];
                    $fromMinute = (int)$value['from_minute'];
                    $toHour = (int)$value['to_hour'];
                    $toMinute = (int)$value['to_minute'];
                    if ($value['is_open'] && ($toHour * 60 + $toMinute <= $fromHour * 60 + $fromMinute)) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('To time must be greater than From time.'));
                        Mage::getSingleton('adminhtml/session')->setFormData($data);
                        if ($placeId) {
                            $this->_redirect('*/*/edit', array('id' => $placeId));
                        } else {
                            $this->_redirect('*/*/new');
                        }
                        return;
                    }
                }
                $data['open_datetime'] = serialize($data['open_datetime']);
            }

            // Serialize store_hour_exceptions before add data

            if (!empty($data['store_hour_exceptions'])) {
                $data['store_hour_exceptions'] = array_filter($data['store_hour_exceptions']);
                if(!empty($data['store_hour_exceptions'])){
                    foreach ($data['store_hour_exceptions'] as $value) {
                        $fromHour = (int)$value['from_hour'];
                        $fromMinute = (int)$value['from_minute'];
                        $toHour = (int)$value['to_hour'];
                        $toMinute = (int)$value['to_minute'];
                        if ($value['is_open'] && ($toHour * 60 + $toMinute <= $fromHour * 60 + $fromMinute)) {
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('To time must be greater than From time.'));
                            Mage::getSingleton('adminhtml/session')->setFormData($data);
                            if ($placeId) {
                                $this->_redirect('*/*/edit', array('id' => $placeId));
                            } else {
                                $this->_redirect('*/*/new');
                            }
                            return;
                        }
                    }
                }
                $data['store_hour_exceptions'] = serialize($data['store_hour_exceptions']);
            }
            else{
                $data['store_hour_exceptions'] = '';
            }
            if(!empty($data['type'])){
                // Validate durations options
                if ((int)$data['type'] == Paypal_Pickup_Model_Place::PICKUP_TYPE_DURATIONS_OPTIONS) {
                    if (!empty($data['durations_options'])) {
                        $periods = explode(',', $data['durations_options']);
                        foreach ($periods as $period) {
                            if (!preg_replace("/[^0-9]/", "", $period) || ((int)$period < 0) || !is_numeric($period)) {
                                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('Durations Options must be a number greater than 0.'));
                                Mage::getSingleton('adminhtml/session')->setFormData($data);
                                if ($placeId) {
                                    $this->_redirect('*/*/edit', array('id' => $placeId));
                                } else {
                                    $this->_redirect('*/*/new');
                                }
                                return;
                            }
                        }
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('Durations Options must not be empty.'));
                        Mage::getSingleton('adminhtml/session')->setFormData($data);
                        if ($placeId) {
                            $this->_redirect('*/*/edit', array('id' => $placeId));
                        } else {
                            $this->_redirect('*/*/new');
                        }
                        return;
                    }
                }
            }


            if ($placeId) {
                $placeModel = Mage::getModel('paypal_pickup/place')->load($placeId);
                $placeModel->addData($data);
            } else {
                /* @var $placeModel Paypal_Pickup_Model_Place */
                $placeModel = Mage::getModel('paypal_pickup/place');
                if ($placeCode && $placeModel->isNewPlace($placeCode)) {
                    $placeModel->setData($data);
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('Place with the same code already exists.'));
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    $this->_redirect('*/*/new');
                    return;
                }
            }
            try {
                Mage::dispatchEvent('paypal_pickup_place_save_before', array('place_model' => $placeModel));

                $placeModel->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('paypal_pickup')->__('Place was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $placeModel->getPlaceId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('Unable to find place to save.'));
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $place_id = $this->getRequest()->getParam('id');
                Mage::dispatchEvent('paypal_pickup_delete_data', array('id' => $place_id));
                $model = Mage::getModel('paypal_pickup/place');
                $model->setId($this->getRequest()->getParam('id'))
                    ->setIsDeleted(1)
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Place was successfully deleted.'));
                Mage::dispatchEvent('paypal_pickup_delete_data_after', array('id' => $place_id));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Mass delete action
     */
    public function massDeleteAction()
    {
        $placeIds = $this->getRequest()->getParam('place');
        Mage::dispatchEvent('paypal_pickup_delete_data', array('id' => $placeIds));
        if (!is_array($placeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select place(s).'));
        } else {
            try {
                foreach ($placeIds as $placeId) {
                    $place = Mage::getModel('paypal_pickup/place')->load($placeId);
                    $place->setIsDeleted(1)->save();
                    Mage::dispatchEvent('paypal_pickup_delete_data_after', array('id' => $placeId));
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted.', count($placeIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/place');
    }
}
