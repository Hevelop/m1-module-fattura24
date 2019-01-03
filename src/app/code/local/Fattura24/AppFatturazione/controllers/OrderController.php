<?php
/*
 * Override del controller che visualizza la fattura lato cliente
 */
require_once 'Mage/Sales/controllers/OrderController.php';

class Fattura24_AppFatturazione_OrderController extends Mage_Sales_OrderController
{
    public function printInvoiceAction()
    {
        $invoiceId = (int) $this->getRequest()->getParam('invoice_id');

        $fattura24Behaviour = false;
        if(Mage::helper('appfatturazione')->getConfig('fattura24/fatture/crea_fattura') == 1)
        {
            $order = Mage::getModel('sales/order_invoice')->load($invoiceId)->getOrder();
            $docId = Mage::helper('appfatturazione')->getDocIdInvoice($order);
            if($docId)
            {
                $fileName = $order->getIncrementId() . '.pdf';
                $filePath = Mage::getBaseDir('media') . "/Fattura24/" . $fileName;
                if(file_exists($filePath) || Mage::helper('appfatturazione')->downloadDocument($docId, $order))
                {
                    $fattura24Behaviour = true;
                }
            }
        }

        if($fattura24Behaviour)
        {
            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename=' . $fileName);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($filePath));
            header('Accept-Ranges: bytes');
            readfile($filePath);
        }
        else
        {
            //$this->alertAndClose("La fattura non è presente. Per maggiori informazioni contatta il titolare del negozio.");
            if ($invoiceId) {
                $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
                $order = $invoice->getOrder();
            } else {
                $orderId = (int) $this->getRequest()->getParam('order_id');
                $order = Mage::getModel('sales/order')->load($orderId);
            }
    
            if ($this->_canViewOrder($order)) {
                Mage::register('current_order', $order);
                if (isset($invoice)) {
                    Mage::register('current_invoice', $invoice);
                }
                $this->loadLayout('print');
                $this->renderLayout();
            } else {
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $this->_redirect('*/*/history');
                } else {
                    $this->_redirect('sales/guest/form');
                }
            }
        }
    }
}