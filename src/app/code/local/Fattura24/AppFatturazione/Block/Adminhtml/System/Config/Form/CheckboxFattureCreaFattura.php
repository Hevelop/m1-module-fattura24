<?php

class Fattura24_AppFatturazione_Block_Adminhtml_System_Config_Form_CheckboxFattureCreaFattura extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $checkbox_value = Mage::helper('appfatturazione')->getConfig('fattura24/fatture/crea_fattura');
        $html = " <input type='hidden' name='groups[fatture][fields][crea_fattura][value]' value='0'>
            <input type='checkbox' name='groups[fatture][fields][crea_fattura][value]' id='fatture_crea_fattura' value='1'";
        if($checkbox_value == 1)
            $html .= " checked";
        $html .= ">";
        return $html;
    }
}