<?php
class Model_DbTable_InvoiceRunningNumber extends Model_DbTable_Base
{
    protected $_name = 'tbl_invoicerunningnumber';
    protected $_primary = 'Irn_Id';

    protected $_parentTableMap = array();
    protected $_childrenTableMap = array();

    public function getMaxInvoiceRunningNumber($buildingPrefix, $bathNumber)
    {
        $selector = $this->select(self::SELECT_WITH_FROM_PART)->from(array("irn" => $this->_name))->columns(array("currentMaxRunningNumber" => new Zend_Db_Expr("max(irn.Irn_CurrentNumber)")));
        $selector->where("irn.Irn_Prefix = '" . $buildingPrefix . "'");
        $selector->where("irn.Irn_BatchId = '"  . $bathNumber . "'");

        return $this->getAdapter()->fetchRow($selector);
    }

    public function incrementRunningNumber($buildingPrefix, $batchId, $incrementedNumber){
        $v['Irn_CurrentNumber'] = $incrementedNumber;
        $where = array("Irn_Prefix = '" . $buildingPrefix . "'", "Irn_BatchId = '" . $batchId . "'");
        return $this->update($v, $where);
    }
}


?>