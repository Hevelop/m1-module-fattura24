<?php

class Fattura24_AppFatturazione_Model_System_Config_Source_SelectOrdiniModelloOrdine
{
    public function toOptionArray()
    {
        return Mage::helper('appfatturazione')->getTemplate(true);
    }
}
?>