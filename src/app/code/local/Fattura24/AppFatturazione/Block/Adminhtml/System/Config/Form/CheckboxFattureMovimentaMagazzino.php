<?php

class Fattura24_AppFatturazione_Block_Adminhtml_System_Config_Form_CheckboxFattureMovimentaMagazzino extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $checkbox_value = Mage::helper('appfatturazione')->getConfig('fattura24/fatture/movimenta_magazzino');
        $html = " <input type='hidden' name='groups[fatture][fields][movimenta_magazzino][value]' value='0'>
            <input type='checkbox' name='groups[fatture][fields][movimenta_magazzino][value]' id='fatture_movimenta_magazzino' value='1'";
        if($checkbox_value == 1)
            $html .= " checked";
        $html .= ">";
        return $html;
    }
}