<?php
class Form_BatchInvoiceRunForm extends Zend_Form
{
    public function __construct($option = null)
    {
        parent::__construct($option);
        $this->setName('batchInvoiceRun');
        $this->setMethod('post');
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/invoice/batchrun');
        $decorator = array(
            array('ViewHelper')
        );

        $batchNumber = new Zend_Form_Element_Text('batchNumber');
        $batchNumber->setAttrib('size', 22)
            ->setRequired(true);
        $batchNumber->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $batchNumber->setDecorators($decorator);

        $overwrite = new Zend_Form_Element_Checkbox('overwrite');
        $overwrite->setRequired(true);
        $overwrite->setChecked(true);
        $overwrite->setDecorators($decorator);

        $building = new Zend_Form_Element_Select('Bul_Id');
        $building->setRequired(true);
        $building->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;width:180px;');
        $building->setDecorators($decorator);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel("RUN INVOICES");

        $this->addElements(array($batchNumber, $overwrite, $building, $submit));
    }
}