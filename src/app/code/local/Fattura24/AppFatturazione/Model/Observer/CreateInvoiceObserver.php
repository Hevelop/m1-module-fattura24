<?php

class Fattura24_AppFatturazione_Model_Observer_CreateInvoiceObserver
{
    public function createInvoice(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getInvoice()->getOrder();  
        if(Mage::helper('appfatturazione')->getConfig('fattura24/fatture/crea_fattura', $order->getStoreId()) == 1 && !Mage::helper('appfatturazione')->getDocIdInvoice($order))
            Mage::helper('appfatturazione')->saveDocument($order,'fattura');
    }
}