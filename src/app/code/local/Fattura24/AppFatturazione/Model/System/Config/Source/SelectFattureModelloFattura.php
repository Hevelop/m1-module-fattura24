<?php

class Fattura24_AppFatturazione_Model_System_Config_Source_SelectFattureModelloFattura
{
    public function toOptionArray()
    {
        return Mage::helper('appfatturazione')->getTemplate(false);
    }
}
