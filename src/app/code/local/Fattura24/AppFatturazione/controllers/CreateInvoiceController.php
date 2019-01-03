<?php
/*
 * Controller associato al pulsante 'Crea fattura' nella tabella degli Ordini
 */
class Fattura24_AppFatturazione_CreateInvoiceController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $orderId = $this->getRequest()->getParam('id');
        $order = Mage::getModel('sales/order')->load($orderId);

        if(Mage::helper('appfatturazione')->getDocIdInvoice($order))
        {
            $this->alert("Fattura già creata in Fattura24.");
            return;
        }

        $checkbox_crea_fattura = Mage::helper('appfatturazione')->getConfig('fattura24/fatture/crea_fattura', $order->getStoreId());
        if($checkbox_crea_fattura != 1)
        {
            $this->alert("La creazione delle fatture con Fattura24 è disabilitata. Per abilitarla spunta la voce \'Crea Fattura\' nelle impostazioni del modulo di Fattura24 in Magento (\'System -> Configuration\').");
            return;
        }

        $dataReturned = Mage::helper('appfatturazione')->saveDocument($order,'fattura');
        $descriptionResponse = explode('</description>', explode('<description>', $dataReturned)[1])[0];
        $returnCode = explode('</returnCode>', explode('<returnCode>', $dataReturned)[1])[0];
        if($returnCode != 0)
            $descriptionResponse .= '\nInoltre ti consigliamo di verificare le impostazioni del modulo di Fattura24 in Magento.';
        else
            $this->tryInvoice($order);

        $this->alert($descriptionResponse);
    }
    
    public function alert($message)
    {
        echo "<script>";
        echo "alert(\"" .  $message . "\");";
        echo "window.location.href='" . Mage::helper('adminhtml')->getUrl('adminhtml/sales_order') . "';";
        echo "</script>";
    }
    
    public function tryInvoice($order)
    {
        if($order->canInvoice())
        {
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
            $invoice->setRequestedCaptureCase($capture);
            $invoice->register();
            
            $transaction = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transaction->save();
            
            if($order->getStatus() == 'pending')
            {
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                $order->save();
            }
        }
    }
}