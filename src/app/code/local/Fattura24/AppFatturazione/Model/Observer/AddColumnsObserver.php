<?php

class Fattura24_AppFatturazione_Model_Observer_AddColumnsObserver
{
    /*
    public function beforeBlockToHtml(Varien_Event_Observer $observer)
    {
        $grid = $observer->getBlock();
        if ($grid instanceof Mage_Adminhtml_Block_Customer_Grid) {
            $grid->addColumnAfter(
                '{column_code}',
                array(
                    'header' => 'Fattura24 - Crea fattura3',
                    'index'  => '{column_code}',
                    'type'   => 'button'
                ),
                'entity_id'
            );
        }
    }
*/
    public function addColumnToGrid(Varien_Event_Observer $observer)
    {        
        $block = $observer->getEvent()->getBlock();
        // Check whether the loaded block is the orders grid block
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid) || $block->getNameInLayout() != 'sales_order.grid')
            return $this;

        // Add a new column rigth after the "view" column
        $block->addColumnAfter('view', [
            'header' => $block->__('Fattura24 - Crea fattura'),
            'width' => '60px',
            'index' => 'fattura24_crea_fattura',
            'type'   => 'action',
            'sortable' => false,
            'getter'   => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('appfatturazione')->__('Crea Fattura'),
                    'url'       => array('base'=> 'fattura24/CreateInvoice/index'),
                    'field'     => 'id'
                ),
            ),
            'filter' => false
        ], 'entity_id');
        
        $block->addColumnAfter('fattura24_crea_fattura', [
            'header' => $block->__('Fattura24 - Visualizza PDF'),
            'width' => '60px',
            'index' => 'fattura24_visualizza_pdf',
            'type'   => 'action',
            'sortable' => false,
            'getter'   => 'getId',
            'actions'   => array(
                    array(
                            'caption'   => Mage::helper('appfatturazione')->__('Visualizza PDF'),
                            'url'       => array('base'=> 'fattura24/ViewInvoice/index'),
                            'field'     => 'id'
                    )
            ),
            'filter' => false
        ], 'entity_id');
        
        $block->addColumnAfter('fattura24_visualizza_pdf', [
            'header' => $block->__('Fattura24 - Aggiorna PDF'),
            'width' => '60px',
            'index' => 'fattura24_aggiorna_pdf',
            'type'   => 'action',
            'sortable' => false,
            'getter'   => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('appfatturazione')->__('Aggiorna PDF'),
                    'url'       => array('base'=> 'fattura24/UpdateInvoice/index'),
                    'field'     => 'id'
                )
            ),
            'filter' => false
        ], 'entity_id');

        return $this;
    }
}