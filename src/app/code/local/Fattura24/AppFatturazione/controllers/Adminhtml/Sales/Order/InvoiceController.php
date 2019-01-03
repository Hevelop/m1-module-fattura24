<?php
/*
 * Override del controller che visualizza la fattura lato amministratore
 */
require_once 'Mage/Adminhtml/controllers/Sales/Order/InvoiceController.php';

class Fattura24_AppFatturazione_Adminhtml_Sales_Order_InvoiceController extends Mage_Adminhtml_Sales_Order_InvoiceController
{
    public function printAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            $fattura24Behaviour = false;
            if (Mage::helper('appfatturazione')->getConfig('fattura24/fatture/crea_fattura') == 1) {
                $order = Mage::getModel('sales/order_invoice')->load($invoiceId)->getOrder();
                $docId = Mage::helper('appfatturazione')->getDocIdInvoice($order);
                if ($docId) {
                    $fileName = $order->getIncrementId().'.pdf';
                    $filePath = Mage::getBaseDir('media').'/Fattura24/'.$fileName;
                    if (file_exists($filePath) || Mage::helper('appfatturazione')->downloadDocument($docId, $order)) {
                        $fattura24Behaviour = true;
                    }
                }
            }

            if ($fattura24Behaviour) {
                header('Content-type: application/pdf');
                header('Content-Disposition: attachment; filename='.$fileName);
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: '.filesize($filePath));
                header('Accept-Ranges: bytes');
                readfile($filePath);
            } else {
                //$this->alertAndComeBack("La fattura creata con Fattura24 non è stata scaricata sul server di Magento. Per scaricarla, premi sul pulsante Scarica Pdf nella tabella degli ordini. Puoi anche attivare la checkbox Scarica Pdf nelle impostazioni del modulo di Fattura24 per scaricarla in automatico quando viene creata.");
                if ($invoice = Mage::getModel('sales/order_invoice')->load($invoiceId)) {
                    $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoice));
                    $this->_prepareDownloadResponse(
                        'invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                        '.pdf', $pdf->render(), 'application/pdf'
                    );
                }
            }
        } else {
            $this->_forward('noRoute');
        }
    }
}
