<?php

class Model_DbTable_Invoices extends Model_DbTable_Base
{
    protected $_name = 'tbl_invoices';
    protected $_primary = 'Inv_Id';

    protected $_dependentTables;
    protected $_childTableMap = array('tbl_payments' => 'Inv_InvoiceNumber = Pay_InvoiceNumber');
    protected $_parentTableMap = array();


}

?>