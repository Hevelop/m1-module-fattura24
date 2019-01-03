<?php
/*
 * Controller associato al pulsante 'Visualizza PDF' nella tabella degli Ordini
 */
class Fattura24_AppFatturazione_ViewInvoiceController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $orderId = $this->getRequest()->getParam('id');
        $order = Mage::getModel('sales/order')->load($orderId);
        $docId = Mage::helper('appfatturazione')->getDocIdInvoice($order);

        if(!$docId)
        {
            $this->alert('La fattura non è stata creata in Fattura24. Premi sul pulsante "Crea fattura" per crearla.');
            return;
        }

        $fileName = $order->getIncrementId() . '.pdf';
        $filePath = Mage::getBaseDir('media') . "/Fattura24/" . $fileName;
        if (!file_exists($filePath))
        {
            $this->alert('La fattura è stata creata in Fattura24 ma non è presente sul server di Magento. Premi sul pulsante "Aggiorna PDF" per scaricarla da Fattura24.');
            return;
        }
        
        header('Content-type: text/html');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filePath));
        header('Accept-Ranges: bytes');
        readfile($filePath);
    }
    
    public function alert($message)
    {
        echo "<script>";
        echo "alert('".  $message . "');";
        echo "window.location.href='" . Mage::helper('adminhtml')->getUrl('adminhtml/sales_order') . "';";
        echo "</script>";
    }
}