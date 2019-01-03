<?php

class Fattura24_AppFatturazione_Block_Adminhtml_System_Config_Form_CheckboxFattureInviaEmail extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $checkbox_value = Mage::helper('appfatturazione')->getConfig('fattura24/fatture/invia_email');
        $html = " <input type='hidden' name='groups[fatture][fields][invia_email][value]' value='0'>
            <input type='checkbox' name='groups[fatture][fields][invia_email][value]' id='fatture_invia_email' value='1'";
        if ($checkbox_value == 1) {
            $html .= ' checked';
        }

        $html .= '>';

        return $html;
    }
}
