<?php

class Fattura24_AppFatturazione_Block_Adminhtml_System_Config_Form_CheckboxOrdiniCreaOrdine extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $checkbox_value = Mage::helper('appfatturazione')->getConfig('fattura24/ordini/crea_ordine');
        $html = " <input type='hidden' name='groups[ordini][fields][crea_ordine][value]' value='0'>
            <input type='checkbox' name='groups[ordini][fields][crea_ordine][value]' id='ordini_crea_ordine' value='1'";
        if($checkbox_value == 1)
            $html .= " checked";
        $html .= ">";
        return $html;
    }
}