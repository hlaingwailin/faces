<?php
class Model_DbTable_Interests extends Model_DbTable_Base
{
    protected $_name = 'tbl_interests';
    protected $_primary = 'Int_Id';

    protected $_parentTableMap = array('tbl_payments' => 'Pay_InvoiceNumber = Int_InvoiceNumber', 'tbl_invoices' => 'Inv_InvoiceNumber = Int_InvoiceNumber');
    protected $_childrenTableMap = array();

}

?>