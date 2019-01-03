<?php

class Fattura24_AppFatturazione_Block_Adminhtml_System_Config_Form_CheckboxOrdiniMovimentaMagazzino extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $checkbox_value = Mage::helper('appfatturazione')->getConfig('fattura24/ordini/movimenta_magazzino');
        $html = " <input type='hidden' name='groups[ordini][fields][movimenta_magazzino][value]' value='0'>
            <input type='checkbox' name='groups[ordini][fields][movimenta_magazzino][value]' id='ordini_movimenta_magazzino' value='1'";
        if ($checkbox_value == 1) {
            $html .= ' checked';
        }

        $html .= '>';

        return $html;
    }
}
