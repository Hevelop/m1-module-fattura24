<?php

class Fattura24_AppFatturazione_Model_System_Config_Source_SelectFattureSezionaleFattura
{
    public function toOptionArray()
    {
        return Mage::helper('appfatturazione')->getNumerator('1');
    }
}
