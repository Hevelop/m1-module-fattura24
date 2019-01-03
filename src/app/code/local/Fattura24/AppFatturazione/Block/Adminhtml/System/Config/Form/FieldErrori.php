<?php

class Fattura24_AppFatturazione_Block_Adminhtml_System_Config_Form_FieldErrori extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->getApiKeyCheckMessage().$this->getVersionCheckMessage();
    }

    public function getApiKeyCheckMessage()
    {
        $apiKeyCheck = Mage::helper('appfatturazione')->testApiKey();
        $apiKey = Mage::helper('appfatturazione')->getConfig('fattura24/generali/api_key');
        $apiKeyMessage = '';
        if (empty($apiKey)) {
            $apiKeyMessage .= $this->getErrorHtml('Api Key non inserita');
        } elseif (!empty($apiKey) && $apiKeyCheck['returnCode'] !== 1) {
            $apiKeyMessage .= $this->getErrorHtml($apiKeyCheck['description']);
        }

        if (!$apiKeyCheck['subscriptionTypeIsValid']) {
            $apiKeyMessage .= $this->getErrorHtml('Attenzione: per utilizzare questo servizio devi avere un abbonamento Business');
        }

        $subscriptionDaysToExpiration = $apiKeyCheck['subscriptionDaysToExpiration'];
        if ($subscriptionDaysToExpiration < 31) {
            $apiKeyMessage .= $this->getNoticeHtml('Mancano '.$subscriptionDaysToExpiration.' giorni alla scadenza del tuo abbonamento');
        }

        return $apiKeyMessage;
    }

    public function checkNewVersion($urlCheckVersion)
    {
        try {
            $fileCheckVersion = fopen($urlCheckVersion, 'r');
            if ($fileCheckVersion) {
                $content = stream_get_contents($fileCheckVersion);
                fclose($fileCheckVersion);

                return $content;
            }
        } catch (Exception $e) {
        }

        return false;
    }

    public function getVersionCheckMessage()
    {
        $versionCheckMessage = '';
        $checkMagentoVersion = $this->checkMagentoVersion();
        if (!$checkMagentoVersion) {
            $versionCheckMessage .= $this->getErrorHtml(
                'Attenzione: questo modulo non è compatibile con la corrente versione di Magento. 
                    Clicca <a href="https://www.fattura24.com/magento-modulo-fatturazione/" target="_blank">qui</a> per scaricare la versione corretta del modulo'
            );
        } else {
            $urlCheckVersion = 'https://www.fattura24.com/magento/latest_version_mag_1.txt';
            $checkNewVersion = $this->checkNewVersion($urlCheckVersion);
            if ($checkNewVersion) {
                $latest_version_date = substr($checkNewVersion, 0, 16);
                if ($latest_version_date != Mage::helper('appfatturazione')->getModuleDate()) {
                    $urlDownload = substr($checkNewVersion, 17);
                    $versionCheckMessage .= $this->getNoticeHtml(
                        'È stata rilasciata una nuova versione del modulo! Clicca <a href="'.$urlDownload.
                        '" target="_blank">qui</a> per scaricarla'
                    );
                }
            }
        }

        return $versionCheckMessage;
    }

    public function checkMagentoVersion()
    {
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, '2.0', '>=')) {
            return false;
        } else {
            return true;
        }
    }

    public function getNoticeHtml($message)
    {
        return '<ul class="messages"><li class="notice-msg">'.$message.'</li></ul>';
    }

    public function getErrorHtml($message)
    {
        return '<ul class="messages"><li class="error-msg">'.$message.'</li></ul>';
    }
}
