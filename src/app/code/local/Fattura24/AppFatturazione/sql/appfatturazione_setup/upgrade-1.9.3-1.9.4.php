<?php
 
$setup = $this;
$setup->startSetup();
$connection = $setup->getConnection();

if(!$connection->isTableExists('fattura24'))
{
    $table = $connection->newTable($setup->getTable('fattura24'))
        ->addColumn(
            'id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id')
        ->addColumn(
            'increment_id_order_magento',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            [],
            'Order Increment Id in Magento')
        ->addColumn(
            'doc_id_order_fattura24',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            [],
            'Id Order Document in Fattura24')
        ->addColumn(
            'doc_id_invoice_fattura24',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            [],
            'Id Invoice Document in Fattura24')
        ->addColumn(
            'last_update',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            255,
            ['nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE],
            'Last update date')
        ->addIndex(
            $setup->getIdxName(
                'fattura24',
                ['increment_id_order_magento'],
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            ),
            ['increment_id_order_magento'],
            ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    );
    $connection->createTable($table);
}

$tableSalesOrderGrid = $setup->getTable('sales_flat_order');
if ($connection->tableColumnExists($tableSalesOrderGrid, 'docIdFattura24'))
{
    $query = "SELECT increment_id, docIdFattura24 FROM " . $tableSalesOrderGrid;
    $result = $connection->fetchAll($query);

    foreach($result as $row)
    {
        $incrementId = $row['increment_id'];
        $docIdFattura24 = $row['docIdFattura24'];
        $query = "INSERT IGNORE INTO " . $setup->getTable('fattura24') . " (increment_id_order_magento, doc_id_invoice_fattura24) VALUES ('" . $incrementId . "','" . $docIdFattura24 . "')"; 
        $setup->getConnection()->query($query);
    }
}

$setup->endSetup();