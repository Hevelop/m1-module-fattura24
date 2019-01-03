<?php
 
$setup = $this;
$setup->startSetup();
    
Mage::getModel('core/config')->saveConfig('fattura24/rubrica/salva_cliente', 1);
Mage::getModel('core/config')->saveConfig('fattura24/ordini/crea_ordine', 1);
Mage::getModel('core/config')->saveConfig('fattura24/fatture/crea_fattura', 1);
Mage::getModel('core/config')->cleanCache();

$setup->endSetup();