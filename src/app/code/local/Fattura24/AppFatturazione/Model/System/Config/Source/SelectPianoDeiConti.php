<?php

class Fattura24_AppFatturazione_Model_System_Config_Source_SelectPianoDeiConti
{
    public function toOptionArray()
    {
        return Mage::helper('appfatturazione')->getPdc();
    }
}