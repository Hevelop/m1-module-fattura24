<?php

class Fattura24_AppFatturazione_Block_Adminhtml_System_Config_Form_FieldVerificaApiKey
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $apiKeyCheck = Mage::helper('appfatturazione')->testApiKey();
        $apiKey = Mage::helper('appfatturazione')->getConfig('fattura24/generali/api_key');
        if(!empty($apiKey) && $apiKeyCheck['returnCode'] == 1)
            return '<div style="color:green;font-size:90%;font-weight:bold;">Api Key verificata</div>';
        else
            return '<div style="color:red;font-size:90%;font-weight:bold;">Api Key non valida</div>';
    }
}