<?php

class Fattura24_AppFatturazione_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getModuleDate()
    {
        return '2018/05/05 14:00';
    }
    
    public function getConfig($configPath, $storeId = null)
    {
        if($storeId == null)
        {
            if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) // store level
                $storeId = Mage::getModel('core/store')->load($code)->getId();
            else if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) // website level
            {
                $websiteId = Mage::getModel('core/website')->load($code)->getId();
                $storeId = Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId();
            }
            else // default level
                $storeId = 0;
        }
        
        return Mage::getStoreConfig($configPath, $storeId);
    }

    function apiCall($fattura24_api_url, $apiKey, $send_data = array())
    {
        $send_data['apiKey'] = $apiKey;
        return $this->curlDownload($fattura24_api_url, http_build_query($send_data));
    }

    function now($fmt = 'Y-m-d H:i:s', $tz = 'Europe/Rome')
    {
        $timestamp = time();
        $dt = new \DateTime("now", new \DateTimeZone($tz));
        $dt->setTimestamp($timestamp);
        return $dt->format($fmt);
    }
    
    function trace()
    {
        Mage::log("\n" . implode("\n", func_get_args()) . "\n\n", null, 'fattura24.log');
    }
    
    function traceObject($description, $object)
    {
        Mage::log("\n" . $description . "\n " . var_export($object->debug(), true) . "\n\n", null, 'fattura24.log');
    }

    function curlDownload($url, $data_string)
    {
        if (!function_exists('curl_init'))
        {
            $this->trace('curl is not installed');
            die('Sorry, cURL is not installed!');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $config = array();
        $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
        curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $output = curl_exec($ch);
        if(curl_errno($ch) != 0)
            $this->trace('curl error', implode("\n", curl_getinfo($ch)), curl_error($ch), curl_errno($ch));
        curl_close($ch);
        return $output;
    }

    function make_strings($v1, $v2)
    {
        $s = is_array($v1) ? trim(implode(' ', $v1)) : $v1;
        if ($s == '')
            $s = is_array($v2) ? trim(implode(' ', $v2)) : $v2;
        return $s;
    }
    
    function writeElement($xml, $tag, $value)
    {
        if(strlen($value) !== 0)
        {
            $xml->startElement($tag);
            $xml->text($value);
            $xml->endElement();
        }
    }

    public function createXml($order, $documentType)
    {
        $this->traceObject('Order details', $order);
        
        $storeId = $order->getStoreId();

        $xml = new \XMLWriter();
        if (!$xml->openMemory())
            throw new \Exception(__('Cannot openMemory', 'fatt-24'));
        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(2);
        $xml->startElement('Fattura24');
        $xml->startElement('Document');
        
        $arrayIva = array();
        
        /* CUSTOMER */
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        if($shippingAddress != null)
        {
            $customerAddress = $this->make_strings($billingAddress->getStreet(),
                                                   $shippingAddress->getStreet());
            $customerPostcode = $this->make_strings($billingAddress->getPostcode(),
                                                    $shippingAddress->getPostcode());
            $customerCity = $this->make_strings($billingAddress->getCity(),
                                                $shippingAddress->getCity());
            $customerProvince = $this->make_strings($billingAddress->getRegion(),
                                                    $shippingAddress->getRegion());
            $customerCountry = $this->make_strings($billingAddress->getCountryId(),
                                                   $shippingAddress->getCountryId());
            $customerCellPhone = $this->make_strings($billingAddress->getTelephone(),
                                                     $shippingAddress->getTelephone());
            $vatId = $this->make_strings($billingAddress->getVatId(),
                                         $shippingAddress->getVatId());
            if(!empty($vatId) && strlen(trim($vatId)) != 16)
                $customerName = $this->make_strings(
                    $this->make_strings($billingAddress->getCompany(), $billingAddress->getName()), 
                    $this->make_strings($shippingAddress->getCompany(), $shippingAddress->getName()));
            else
                $customerName = $this->make_strings(
                    $this->make_strings($billingAddress->getName(), $billingAddress->getCompany()), 
                    $this->make_strings($shippingAddress->getName(), $shippingAddress->getCompany()));
        }
        else
        {
            $customerAddress = $this->make_strings($billingAddress->getStreet(),'');
            $customerPostcode = $this->make_strings($billingAddress->getPostcode(),'');
            $customerCity = $this->make_strings($billingAddress->getCity(),'');
            $customerProvince = $this->make_strings($billingAddress->getRegion(),'');
            $customerCountry = $this->make_strings($billingAddress->getCountryId(),'');
            $customerCellPhone = $this->make_strings($billingAddress->getTelephone(),'');
            $vatId = $this->make_strings($billingAddress->getVatId(),'');
            if(!empty($vatId) && strlen(trim($vatId)) != 16)
                $customerName = $this->make_strings($billingAddress->getCompany(), $billingAddress->getName());
            else
                $customerName = $this->make_strings($billingAddress->getName(), $billingAddress->getCompany());
        }
        if(strlen(trim($vatId)) == 16)
        {
            $customerFiscalCode = $vatId;
            $customerVatCode = '';
        }
        else
        {
            $customerFiscalCode = '';
            $customerVatCode = $vatId;
        }
        $customerTaxWat = $order->getCustomerTaxvat();
        if(!empty($customerTaxWat))
        {
            if(strlen(trim($customerTaxWat)) == 16)
                $customerFiscalCode = $customerTaxWat;
            else if(empty($customerVatCode))
                $customerVatCode = $customerTaxWat;
        }
        $customerEmail = $order->getCustomerEmail();

        $customerData = array
        (
            'Name'      => $customerName,
            'Address'   => $customerAddress,
            'Postcode'  => $customerPostcode,
            'City'      => $customerCity,
            'Province'  => $customerProvince,
            'Country'   => $customerCountry,
            'CellPhone' => $customerCellPhone,
            'FiscalCode'=> $customerFiscalCode,
            'VatCode'   => $customerVatCode,
            'Email'     => $customerEmail
        );
        foreach($customerData as $k => $v)
            $this->writeElement($xml, 'Customer'.$k, $v);
        
        if($documentType == 'customer')
        {
            $xml->endElement(); // end Document
            $xml->endElement(); // end Fattura24
            $xml->endDocument(); // final

            return $xml->outputMemory(TRUE);
        }
        
        /* DELIVERY */
        if($shippingAddress !== null)
        {
            $deliveryName = $shippingAddress->getName();
            $deliveryAddress = trim(implode(' ', $shippingAddress->getStreet()));
            $deliveryPostcode = $shippingAddress->getPostcode();
            $deliveryCity = $shippingAddress->getCity();
            $deliveryProvince = $shippingAddress->getRegion();
            $deliveryCountry = $shippingAddress->getCountryId();
            
            $customerDeliveryData = array
            (
                'Name'      => $deliveryName,
                'Address'   => $deliveryAddress,
                'Postcode'  => $deliveryPostcode,
                'City'      => $deliveryCity,
                'Province'  => $deliveryProvince,
                'Country'   => $deliveryCountry,
            );

            foreach($customerDeliveryData as $k => $v)
                $this->writeElement($xml, 'Delivery'.$k, $v);
        }
        
        $total = $order->getGrandTotal();
        $vatAmount = $order->getTaxAmount();
        $totalWithoutTax = $total - $vatAmount;
        
        $this->writeElement($xml, 'TotalWithoutTax', $totalWithoutTax);
        $this->writeElement($xml, 'VatAmount', $vatAmount);
        $this->writeElement($xml, 'Total', $total);
        
        $payment = $order->getPayment();
        $paymentMethod = $payment->getMethodInstance();
        $paymentMethodCode = $paymentMethod->getCode();
        if($paymentMethodCode == 'banktransfer')
            $paymentMethodName = 'Bonifico bancario';
        else if ($paymentMethodCode == 'cashondelivery')
            $paymentMethodName = 'Contrassegno';
        else if ($paymentMethodCode == 'checkmo')
            $paymentMethodName = 'Assegno / Vaglia Postale';
        else
            $paymentMethodName = $paymentMethodCode;
        $paymentMethodDescription = $paymentMethod->getTitle();        
        $this->writeElement($xml, 'PaymentMethodName', $paymentMethodName);
        $this->writeElement($xml, 'PaymentMethodDescription', $paymentMethodDescription);     
        
        $xml->startElement('Payments');
        $xml->startElement('Payment');
        $this->writeElement($xml, 'Date', $this->now('Y-m-d'));
        $this->writeElement($xml, 'Amount', $total);        
        if ($documentType != 'ordine')
        {
            if($this->getConfig('fattura24/fatture/stato_pagata', $storeId) == 1)
                $this->writeElement($xml, 'Paid', 'true');
            else
                $this->writeElement($xml, 'Paid', 'false');
        }
        $xml->endElement(); // end Payment
        $xml->endElement(); // end Payments
        
        /* ITEMS */
        $xml->startElement('Rows');
        $pdcIsConfigured = false;
        $orderHasDiscount = false;
        if($this->getConfig('fattura24/fatture/crea_fattura', $storeId) == 1)
        {
            $idPdc = $this->getConfig('fattura24/fatture/pdc', $storeId);
            if(!empty($idPdc) && $idPdc != 'Nessun Pdc')
                $pdcIsConfigured = true;
        }
        foreach ($order->getAllItems() as $item)
        {
            $this->traceObject('Item details', $item);
            
            if($documentType == 'ordine')
            {
                $qty = $item->getQtyOrdered();
            }
            else if($documentType == 'fattura')
            {
                $qty = $item->getQtyOrdered() - $item->getQtyCanceled();
            }

            if($qty > 0)
            {
                $vatCode = $item->getTaxPercent();
                $price = $item->getRowTotal() / $qty;

                // gestione prodotti configurabili
                if($item->getProduct()->isConfigurable())
                {
                    $code = $item->getProductOptions()['simple_sku'];
                    $description = $item->getProductOptions()['simple_name'];
                }
                else
                {
                    if(!empty($code) && $item->getSku() == $code)
                        continue;
                    $code = $item->getSku();
                    $description = $item->getName();
                }            
                $price = $item->getBasePrice();            
                $vatCode = $item->getTaxPercent();

                // gestione prodotti con diverse Iva scontati con stesso coupon 
                if($item->getDiscountAmount() != 0)
                {
                    $orderHasDiscount = true;
                    $stringVatCode = trim(' '. $vatCode);
                    if(!array_key_exists($stringVatCode, $arrayIva))
                        $arrayIva[$stringVatCode] = $item->getDiscountAmount() * 100 / (100 + $vatCode);
                    else
                        $arrayIva[$stringVatCode] += $item->getDiscountAmount() * 100 / (100 + $vatCode);
                }                
                
                $xml->startElement('Row');
                $this->writeElement($xml, 'Code', $code);
                $this->writeElement($xml, 'Description', $description);
                $this->writeElement($xml, 'Qty', $qty);
                $this->writeElement($xml, 'Price', $price);
                $this->writeElement($xml, 'VatCode', $vatCode);
                if($pdcIsConfigured)
                    $this->writeElement($xml, 'IdPdc', $idPdc);
                $xml->endElement(); // end Row
            }
        }
        
        if($orderHasDiscount)
        {
            foreach ($arrayIva as $iva => $amount)
            {   
                $description = 'Sconto';
                $couponCode = $order->getCouponCode();
                if(!empty($couponCode))
                    $description .= ' (' . $couponCode . ')';
                $price = - $amount;

                $xml->startElement('Row');
                $this->writeElement($xml, 'Description', $description);
                $this->writeElement($xml, 'Qty', '1');
                $this->writeElement($xml, 'Price', $price);
                $this->writeElement($xml, 'VatCode', $iva);
                if($pdcIsConfigured)
                    $this->writeElement($xml, 'IdPdc', $idPdc);
                $xml->endElement(); // end Row
            }
        }
        
        if($order->getShippingAmount() != 0)
        {
            $description = $order->getShippingDescription();
            $price = $order->getShippingAmount();
            $vatCode = round($order->getShippingTaxAmount() / $order->getShippingAmount() * 100);
            
            $xml->startElement('Row');
            $this->writeElement($xml, 'Description', $description);
            $this->writeElement($xml, 'Qty', '1');
            $this->writeElement($xml, 'Price', $price);
            $this->writeElement($xml, 'VatCode', $vatCode);
            if($pdcIsConfigured)
                $this->writeElement($xml, 'IdPdc', $idPdc);
            $xml->endElement(); // end Row
        }
        
        $xml->endElement(); // end Rows
        
        if($documentType == 'ordine')
        {
            $this->writeElement($xml, 'Number', $order->getIncrementId());
            $documentType = 'C';
            $sendEmail = $this->getConfig('fattura24/ordini/invia_email', $storeId) == 1 ? 'true' : 'false';
            $updateStorage = $this->getConfig('fattura24/ordini/movimenta_magazzino', $storeId);
            if($shippingAddress == null)
                $idTemplate =  $this->getConfig('fattura24/ordini/modello_ordine', $storeId);
            else
                $idTemplate =  $this->getConfig('fattura24/ordini/modello_ordine_accompagnatorio', $storeId);
        }
        else if($documentType == 'fattura')
        {
            $this->writeElement($xml, 'Object', 'Ordine Magento N. ' . $order->getIncrementId());
            $this->writeElement($xml, 'FootNotes', 'Ordine numero ' . $order->getIncrementId());
            if($docIdOrderFattura24 = $this->getDocIdOrder($order))
                $this->writeElement($xml, 'F24OrderId', $docIdOrderFattura24);
            $documentType = $this->getConfig('fattura24/fatture/disabilita_ricevute', $storeId) == 1 ? 'I-force' : 'I';
            if($documentType == 'I' && empty($customerVatCode)) // receipt
                $idNumerator = $this->getConfig('fattura24/fatture/sezionale_ricevuta', $storeId);
            else // invoice
                $idNumerator = $this->getConfig('fattura24/fatture/sezionale_fattura', $storeId);
            if($idNumerator !== 'Predefinito')
                $this->writeElement($xml, 'IdNumerator', $idNumerator);
            $sendEmail = $this->getConfig('fattura24/fatture/invia_email', $storeId) == 1 ? 'true' : 'false';
            $updateStorage = $this->getConfig('fattura24/fatture/movimenta_magazzino', $storeId);
            if($shippingAddress == null)
                $idTemplate =  $this->getConfig('fattura24/fatture/modello_fattura', $storeId);
            else
                $idTemplate =  $this->getConfig('fattura24/fatture/modello_fattura_accompagnatoria', $storeId);
        }
        $this->writeElement($xml, 'DocumentType', $documentType);
        $this->writeElement($xml, 'SendEmail', $sendEmail);
        $this->writeElement($xml, 'UpdateStorage', $updateStorage);
        if($idTemplate !== 'Predefinito')
            $this->writeElement($xml, 'IdTemplate', $idTemplate);

        $xml->endElement(); // end Document
        $xml->endElement(); // end Fattura24
        $xml->endDocument(); // final
        
        return $xml->outputMemory(TRUE);
    }
    
    public function saveCustomer($order)
    {
        $fattura24_api_url = 'https://www.app.fattura24.com/api/v0.3/SaveCustomer';
        $send_data = array();
        $storeId = $order->getStoreId();
        $send_data['apiKey'] = $this->getConfig('fattura24/generali/api_key', $storeId);
        $send_data['xml'] = $this->createXml($order, 'customer');

        $this->trace('Creazione cliente (Store Id: ' . $storeId . ')', 'Dati inviati:', $send_data['xml'], 'Dati ricevuti:', $dataReturned);

        $dataReturned = $this->curlDownload($fattura24_api_url, http_build_query($send_data));

        return $dataReturned;
    }
    
    public function saveDocument($order, $documentType)
    {
        $send_data = array();
        $storeId =  $order->getStoreId();
        $send_data['xml'] = $this->createXml($order, $documentType);
        $apiKey = $this->getConfig('fattura24/generali/api_key', $storeId);
        // $this->trace('Before encoding', $send_data['xml']);
        // $this->trace('After encoding', http_build_query($send_data['xml']));
        $dataReturned = $this->apiCall('https://www.app.fattura24.com/api/v0.3/SaveDocument', $apiKey, $send_data);

        $this->trace('Creazione documento (Store Id: ' . $storeId . ')', 'Dati inviati:', $send_data['xml'], 'Dati ricevuti:', $dataReturned);     

        $docId = explode('</docId>', explode('<docId>', $dataReturned)[1])[0];
        $incrementId = $order->getIncrementId();
        try
        {
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            $writeConnection->beginTransaction();
            $fattura24Table = $resource->getTableName('fattura24');

            $query = "INSERT IGNORE INTO " . $fattura24Table . " (increment_id_order_magento) VALUES ('" . $incrementId . "')";
            $writeConnection->query($query);
            if($documentType == 'ordine')
            {
                $query = "UPDATE " . $fattura24Table . " SET doc_id_order_fattura24='" . $docId . "' WHERE increment_id_order_magento='" . $incrementId . "'";
                $writeConnection->query($query);
            }
            else // $documentType == 'fattura'
            {
                $this->downloadDocument($docId, $order);
                $query = "UPDATE " . $fattura24Table . " SET doc_id_invoice_fattura24='" . $docId . "' WHERE increment_id_order_magento='" . $incrementId . "'";
                $writeConnection->query($query);
            }
            $writeConnection->commit();
        }
        catch (Exception $e)
        {
            $writeConnection->rollback();
        }   
        
        return $dataReturned;
    }
    
    public function downloadDocument($docId, $order)
    {
        $send_data = array();
        $send_data['docId'] = $docId;
        $apiKey = $this->getConfig('fattura24/generali/api_key', $order->getStoreId());
        $dataReturned = $this->apiCall('https://www.app.fattura24.com/api/v0.3/GetFile', $apiKey, $send_data);
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string(utf8_encode($dataReturned));
        if (is_object($xml) && $xml->returnCode == -1)
            return false;
        else
        {
            $pdfDirectory = Mage::getBaseDir('media') . "/Fattura24";
            if(!file_exists($pdfDirectory))
                mkdir($pdfDirectory, 0777);
            $fileName = $order->getIncrementId() . '.pdf';
            file_put_contents($pdfDirectory . '/' . $fileName, $dataReturned);
            return true;
        }
    }
    
    /*
     * Get Template List
     */
    function getTemplate($isOrder)
    {
        $apiKey = $this->getConfig('fattura24/generali/api_key');
        $dataReturned = $this->apiCall('https://www.app.fattura24.com/api/v0.3/GetTemplate', $apiKey);
        $listaNomi = array();
        $listaNomi['Predefinito'] = 'Predefinito';
        $xml = simplexml_load_string(utf8_encode($dataReturned));
        if (is_object($xml))
        {
            $listaModelli = $isOrder ? $xml->modelloOrdine : $xml->modelloFattura;
            foreach($listaModelli as $modello)
                $listaNomi[intval($modello->id)] = strval($modello->descrizione) . " (ID: " . intval($modello->id) . ")";
        }
        else
            $this->trace('error list templates', $dataReturned);
        return $listaNomi;
    }
    
    /*
     * Get Pdc List
     */
    function getPdc()
    {
        $apiKey = $this->getConfig('fattura24/generali/api_key');
        $dataReturned = $this->apiCall('https://www.app.fattura24.com/api/v0.3/GetPdc', $apiKey);
        $listaNomi = array();
        $listaNomi['Nessun Pdc'] = 'Nessun Pdc';
        $xml = simplexml_load_string(utf8_encode($dataReturned));
        if (is_object($xml))
        {
            foreach($xml->pdc as $pdc)
                if(intval($pdc->ultimoLivello) == 1)
                    $listaNomi[intval($pdc->id)] = str_replace('^', '.', strval($pdc->codice)) . ' - ' . strval($pdc->descrizione);
        }
        else
            $this->trace('error list pdc', $dataReturned);
        return $listaNomi;
    }

    /*
    * Get Sezionale List
    */
    public function getNumerator($idTipoDocumento)
    {
        $apiKey = $this->getConfig('fattura24/generali/api_key');
        $dataReturned = $this->apiCall('https://www.app.fattura24.com/api/v0.3/GetNumerator', $apiKey);
        $listaNomi = array();
        $listaNomi['Predefinito'] = 'Predefinito';
        $xml = simplexml_load_string(utf8_encode($dataReturned));
        if (is_object($xml))
        {
            foreach($xml->sezionale as $sezionale)
                foreach($sezionale->doc as $doc)
                    if(strval($doc->id) == $idTipoDocumento && intval($doc->stato) == 1)
                        $listaNomi[intval($sezionale->id)] = strval($sezionale->code);
        }
        else
            $this->trace('error list sezionale', $dataReturned);
        return $listaNomi;
    }

    public function testApiKey()
    {
        $apiKey = $this->getConfig('fattura24/generali/api_key');
        $dataReturned = $this->apiCall('https://www.app.fattura24.com/api/v0.3/TestKey', $apiKey);
        $xml = simplexml_load_string(str_replace('&egrave;', 'è', $dataReturned));
        $subscriptionTypeIsValid = true;
        $subscriptionDaysToExpiration = 365;
        if(is_object($xml))
        {
            $returnCode = intval($xml->returnCode);
            $description = strval($xml->description);
            if($returnCode == 1)
            {
                $subscriptionType = intval($xml->subscription->type);
                if($subscriptionType != 5 && $subscriptionType != 6)
                    $subscriptionTypeIsValid = false;
                $subscriptionExpire = strval($xml->subscription->expire);
                $date1 = $this->now();
                $date2 = str_replace('/', '-', $subscriptionExpire);
                $diff = abs(strtotime($date1) - strtotime($date2));
                $subscriptionDaysToExpiration = ceil($diff / 86400);                
            }
        }
        else
        {
            $returnCode = '?';
            $description = 'Errore generico, per favore contatta il nostro servizio tecnico a info@fattura24.com';
        }
        return array(
            'returnCode' => $returnCode,
            'description' => $description,
            'subscriptionTypeIsValid' => $subscriptionTypeIsValid,
            'subscriptionDaysToExpiration' =>  $subscriptionDaysToExpiration
        );
    }
    
    function getDocIdInvoice($order)
    {
        try
        {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $readConnection->beginTransaction();
            $tableName = $resource->getTableName('fattura24');
            $incrementId = $order->getIncrementId();
            
            $query = "SELECT doc_id_invoice_fattura24 FROM " . $tableName . " WHERE increment_id_order_magento='" . $incrementId . "'";
            $result = $readConnection->fetchOne($query);
            $readConnection->commit();
            
            if(empty($result))
                return false;
            return $result;
        }
        catch (Exception $e)
        {
            $readConnection->rollback();
        }
    }
    
    function getDocIdOrder($order)
    {
        try
        {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $readConnection->beginTransaction();
            $tableName = $resource->getTableName('fattura24');
            $incrementId = $order->getIncrementId();
            
            $query = "SELECT doc_id_order_fattura24 FROM " . $tableName . " WHERE increment_id_order_magento='" . $incrementId . "'";
            $result = $readConnection->fetchOne($query);
            $readConnection->commit();
            
            if(empty($result))
                return false;
            return $result;
        }
        catch (Exception $e)
        {
            $readConnection->rollback();
        }
    }
}