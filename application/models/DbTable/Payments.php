<?php

class Model_DbTable_Payments extends Model_DbTable_Base
{
    protected $_name = 'tbl_payments';
    protected $_primary = 'Pay_Id';

    protected $_parentTableMap = array(
        'tbl_invoices' => 'Pay_InvoiceNumber = Inv_InvoiceNumber'
    );
}

?>