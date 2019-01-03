<?php

class Fattura24_AppFatturazione_Block_Adminhtml_System_Config_Form_CheckboxFattureDisabilitaRicevute extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $checkbox_value = Mage::helper('appfatturazione')->getConfig('fattura24/fatture/disabilita_ricevute');
        $html = " <input type='hidden' name='groups[fatture][fields][disabilita_ricevute][value]' value='0'>
            <input type='checkbox' name='groups[fatture][fields][disabilita_ricevute][value]' id='fatture_disabilita_ricevute' value='1'";
        if ($checkbox_value == 1) {
            $html .= ' checked';
        }

        $html .= '>';

        $checkbox_crea_ordine = Mage::helper('appfatturazione')->getConfig('fattura24/ordini/crea_ordine');
        $checkbox_crea_fattura = Mage::helper('appfatturazione')->getConfig('fattura24/fatture/crea_fattura');
        $javaScript = "
            <script type=\"text/javascript\">
                var checkboxRubricaSalvaCliente = document.getElementById('rubrica_salva_cliente');
                var checkboxOrdiniCreaOrdine = document.getElementById('ordini_crea_ordine');
                var checkboxOrdiniInviaEmail = document.getElementById('ordini_invia_email');
                var checkboxOrdiniMovimentaMagazzino = document.getElementById('ordini_movimenta_magazzino');
                var selectOrdiniModelloOrdine = document.getElementById('fattura24_ordini_modello_ordine');
                var selectOrdiniModelloOrdineAccompagnatorio = document.getElementById('fattura24_ordini_modello_ordine_accompagnatorio');
                var checkboxFattureCreaFattura = document.getElementById('fatture_crea_fattura');
                var checkboxFattureInviaEmail = document.getElementById('fatture_invia_email');
                var checkboxFattureMovimentaMagazzino = document.getElementById('fatture_movimenta_magazzino');
                var checkboxFattureStatoPagata = document.getElementById('fatture_stato_pagata');
                var checkboxFattureDisabilitaRicevute = document.getElementById('fatture_disabilita_ricevute');
                var selectFattureSezionaleRicevute = document.getElementById('fattura24_fatture_sezionale_ricevuta');
                var selectFattureSezionaleFatture = document.getElementById('fattura24_fatture_sezionale_fattura');                
                var selectFattureModelloFattura = document.getElementById('fattura24_fatture_modello_fattura');
                var selectFattureModelloFatturaAccompagnatoria = document.getElementById('fattura24_fatture_modello_fattura_accompagnatoria');
                var selectFatturePdc = document.getElementById('fattura24_fatture_pdc');

                checkboxOrdiniCreaOrdine.addEventListener('change', function()
                {
                    var val = this.checked;
                    checkboxOrdiniInviaEmail.readOnly = !val;
                    checkboxOrdiniInviaEmail.disabled = !val;
                    checkboxOrdiniMovimentaMagazzino.readOnly = !val;
                    checkboxOrdiniMovimentaMagazzino.disabled = !val;
                    selectOrdiniModelloOrdine.readOnly = !val;
                    selectOrdiniModelloOrdine.disabled = !val;
                    selectOrdiniModelloOrdineAccompagnatorio.readOnly = !val;
                    selectOrdiniModelloOrdineAccompagnatorio.disabled = !val;
            
                    checkboxRubricaSalvaCliente.readOnly = val;
                    checkboxRubricaSalvaCliente.disabled = val;
                    checkboxRubricaSalvaCliente.checked = val;
                    if (!val)
                    {
                        checkboxOrdiniInviaEmail.checked = val;
                        checkboxOrdiniMovimentaMagazzino.checked = val;
                        if(!checkboxFattureCreaFattura.checked)
                        checkboxRubricaSalvaCliente.checked = !val;
                        else
                        {
                            checkboxRubricaSalvaCliente.checked = !val;
                            checkboxRubricaSalvaCliente.readOnly = !val;
                            checkboxRubricaSalvaCliente.disabled = !val;
                        }
                    }
                })    
                
                checkboxFattureCreaFattura.addEventListener('change', function()
                {
                    var val = this.checked;
                    checkboxFattureInviaEmail.disabled = !val;
                    checkboxFattureInviaEmail.readOnly = !val;
                    checkboxFattureStatoPagata.disabled = !val;
                    checkboxFattureStatoPagata.readOnly = !val;
                    checkboxFattureDisabilitaRicevute.disabled = !val;
                    checkboxFattureDisabilitaRicevute.readOnly = !val;
                    selectFattureSezionaleRicevute.disabled = !val;
                    selectFattureSezionaleRicevute.readOnly = !val;
                    selectFattureSezionaleFatture.disabled = !val;
                    selectFattureSezionaleFatture.readOnly = !val;
                    checkboxFattureMovimentaMagazzino.disabled = !val;
                    checkboxFattureMovimentaMagazzino.readOnly = !val;
                    selectFattureModelloFattura.disabled = !val;
                    selectFattureModelloFattura.readOnly = !val;
                    selectFattureModelloFatturaAccompagnatoria.disabled = !val;
                    selectFattureModelloFatturaAccompagnatoria.readOnly = !val;
                    selectFatturePdc.disabled = !val;
                    selectFatturePdc.readOnly = !val;
            
                    checkboxRubricaSalvaCliente.readOnly = val;
                    checkboxRubricaSalvaCliente.disabled = val;
                    checkboxRubricaSalvaCliente.checked = val;
                    if (!val)
                    {
                        checkboxFattureInviaEmail.checked = val;
                        checkboxFattureStatoPagata.checked = val;
                        checkboxFattureDisabilitaRicevute.checked = val;
                        selectFattureSezionaleRicevute.checked = val;
                        selectFattureSezionaleFatture.checked = val;
                        checkboxFattureMovimentaMagazzino.checked = val;
                        if(!checkboxOrdiniCreaOrdine.checked)
                        checkboxRubricaSalvaCliente.checked = !val;
                        else
                        {
                            checkboxRubricaSalvaCliente.checked = !val;
                            checkboxRubricaSalvaCliente.readOnly = !val;
                            checkboxRubricaSalvaCliente.disabled = !val;
                        }
                    }
                })
            
                function inizializza(boolCreaOrdine, boolCreaFattura)
                {
                    var val1 = boolCreaOrdine;
                    var val2 = boolCreaFattura;

                    checkboxOrdiniInviaEmail.readOnly = !val1;
                    checkboxOrdiniInviaEmail.disabled = !val1;
                    checkboxOrdiniMovimentaMagazzino.readOnly = !val1;
                    checkboxOrdiniMovimentaMagazzino.disabled = !val1;
                    selectOrdiniModelloOrdine.readOnly = !val1;
                    selectOrdiniModelloOrdine.disabled = !val1;
                    selectOrdiniModelloOrdineAccompagnatorio.readOnly = !val1;
                    selectOrdiniModelloOrdineAccompagnatorio.disabled = !val1;
                    if (!val1)
                    {
                        checkboxOrdiniInviaEmail.checked = val1;
                        checkboxOrdiniMovimentaMagazzino.checked = val1;
                    }
            
                    checkboxFattureInviaEmail.readOnly = !val2;
                    checkboxFattureInviaEmail.disabled = !val2;
                    checkboxFattureMovimentaMagazzino.readOnly = !val2;
                    checkboxFattureMovimentaMagazzino.disabled = !val2;
                    checkboxFattureStatoPagata.readOnly = !val2;
                    checkboxFattureStatoPagata.disabled = !val2;
                    checkboxFattureDisabilitaRicevute.readOnly = !val2;
                    checkboxFattureDisabilitaRicevute.disabled = !val2;
                    selectFattureSezionaleRicevute.readOnly = !val2;
                    selectFattureSezionaleRicevute.disabled = !val2;
                    selectFattureSezionaleFatture.readOnly = !val2;
                    selectFattureSezionaleFatture.disabled = !val2;
                    selectFattureModelloFattura.readOnly = !val2;
                    selectFattureModelloFattura.disabled = !val2;
                    selectFattureModelloFatturaAccompagnatoria.readOnly = !val2;
                    selectFattureModelloFatturaAccompagnatoria.disabled = !val2;
                    selectFatturePdc.readOnly = !val2;
                    selectFatturePdc.disabled = !val2;
                    if (!val2)
                    {
                        checkboxFattureInviaEmail.checked = val2;
                        checkboxFattureMovimentaMagazzino.checked = val2;
                        checkboxFattureStatoPagata.checked = val2;
                        checkboxFattureDisabilitaRicevute.checked = val2;
                        selectFattureSezionaleRicevute.checked = val2;
                        selectFattureSezionaleFatture.checked = val2;
                    }
                    if (val1 || val2)
                    {
                        checkboxRubricaSalvaCliente.checked = true;
                        checkboxRubricaSalvaCliente.readOnly = true;
                        checkboxRubricaSalvaCliente.disabled = true;
                    }
                }
                                
                inizializza({$checkbox_crea_ordine}, {$checkbox_crea_fattura});
            </script>";

        $html .= $javaScript;

        return $html;
    }
}
