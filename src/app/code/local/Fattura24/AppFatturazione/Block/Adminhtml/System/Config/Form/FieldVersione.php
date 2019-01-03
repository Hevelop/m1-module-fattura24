<?php

class Fattura24_AppFatturazione_Block_Adminhtml_System_Config_Form_FieldVersione
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return (string) Mage::getConfig()->getNode()->modules->Fattura24_AppFatturazione->version;
    }
}