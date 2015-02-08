<?php
class Form_BuildingsForm extends Zend_Form
{
    public function __construct($option = null)
    {
        parent::__construct($option);
        $this->setName('buildings');
        $this->setMethod('post');
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/edit/buildings');
        $decorator = array(
            array('ViewHelper')
        );

        $id = new Zend_Form_Element_Hidden('Bul_Id');
        $id->setDecorators($decorator);

        $name = new Zend_Form_Element_Text('Bul_Name');
        $name->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Building Name');
        $name->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $name->setDecorators($decorator);

        $address1 = new Zend_Form_Element_Text('Bul_Address1');
        $address1->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Address 1');
        $address1->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $address1->setDecorators($decorator);

        $address2 = new Zend_Form_Element_Text('Bul_Address2');
        $address2->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Address 2');
        $address2->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $address2->setDecorators($decorator);

        $postalCode = new Zend_Form_Element_Text('Bul_PostalCode');
        $postalCode->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $postalCode->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $postalCode->setDecorators($decorator);

        $invoicePrefix = new Zend_Form_Element_Text('Bul_InvoicePrefix');
        $invoicePrefix->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Invoice Prefix');
        $invoicePrefix->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $invoicePrefix->setDecorators($decorator);

        $buildingOperator = new Zend_Form_Element_Select('Bul_OperatorId');
        $buildingOperator->setAttrib('size', 1)
            ->setRequired(true)
            ->setLabel('Operator Name');
        $buildingOperator->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;width:210px;');
        $buildingOperator->setDecorators($decorator);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');

        $this->addElements(array($id, $name, $address1, $address2, $postalCode, $invoicePrefix, $buildingOperator, $submit));
    }
}