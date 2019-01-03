<?php

class Fattura24_AppFatturazione_Model_Observer_CreateOrderObserver
{
    public function createOrder(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();
        if (Mage::helper('appfatturazione')->getConfig('fattura24/ordini/crea_ordine', $storeId) == 1) {
            Mage::helper('appfatturazione')->saveDocument($order, 'ordine');
        } elseif (Mage::helper('appfatturazione')->getConfig('fattura24/rubrica/salva_cliente', $storeId) == 1) {
            Mage::helper('appfatturazione')->saveCostumer($order);
        }
    }
}
