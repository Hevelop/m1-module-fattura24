<?php
/*
 * Controller associato al pulsante 'Aggiorna PDF' nella tabella degli Ordini
 */
class Fattura24_AppFatturazione_UpdateInvoiceController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $orderId = $this->getRequest()->getParam('id');
        $order = Mage::getModel('sales/order')->load($orderId);
        $docId = Mage::helper('appfatturazione')->getDocIdInvoice($order);

        if (!$docId) {
            $this->alert('La fattura non è stata creata in Fattura24. Premi sul pulsante "Crea Fattura" per crearla.');

            return;
        }

        if (Mage::helper('appfatturazione')->downloadDocument($docId, $order)) {
            $this->alert('Download da Fattura24 completato.');
        } else {
            $this->alert('Documento non presente in Fattura24.');
        }
    }

    public function alert($message)
    {
        echo '<script>';
        echo "alert('".$message."');";
        echo "window.location.href='".Mage::helper('adminhtml')->getUrl('adminhtml/sales_order')."';";
        echo '</script>';
    }
}
