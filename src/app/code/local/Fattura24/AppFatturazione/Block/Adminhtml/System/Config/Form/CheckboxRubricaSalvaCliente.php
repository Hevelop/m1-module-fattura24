<?php

class Fattura24_AppFatturazione_Block_Adminhtml_System_Config_Form_CheckboxRubricaSalvaCliente extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $checkbox_value = Mage::helper('appfatturazione')->getConfig('fattura24/rubrica/salva_cliente');
        $html = " <input type='hidden' name='groups[rubrica][fields][salva_cliente][value]' id = 'rubrica_salva_cliente_hidden' value='0'>
            <input type='checkbox' name='groups[rubrica][fields][salva_cliente][value]' id='rubrica_salva_cliente' value='1'";
        if($checkbox_value == 1)
            $html .= " checked";
        $html .= ">";
        return $html;
    }
}