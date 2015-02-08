<?php
class Form_ShopOperatorsForm extends Zend_Form
{
    public function __construct($option = null)
    {
        parent::__construct($option);
        $this->setName('shopoperator');
        $this->setMethod('post');
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/edit/shopoperators');
        $decorator = array(
            array('ViewHelper')
        );

        $id = new Zend_Form_Element_Hidden('Sop_Id');
        $id->setDecorators($decorator);

        $name = new Zend_Form_Element_Text('Sop_Name');
        $name->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Company Name');
        $name->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $name->setDecorators($decorator);

        $address1 = new Zend_Form_Element_Text('Sop_MailingAddress1');
        $address1->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Address 1');
        $address1->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $address1->setDecorators($decorator);

        $address2 = new Zend_Form_Element_Text('Sop_MailingAddress2');
        $address2->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Address 2');
        $address2->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $address2->setDecorators($decorator);

        $postalCode = new Zend_Form_Element_Text('Sop_PostalCode');
        $postalCode->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $postalCode->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $postalCode->setDecorators($decorator);

        $attention = new Zend_Form_Element_Text('Sop_Attention');
        $attention->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $attention->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $attention->setDecorators($decorator);

        $mainContact = new Zend_Form_Element_Text('Sop_MainContactPerson');
        $mainContact->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $mainContact->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $mainContact->setDecorators($decorator);

        $giroAccount = new Zend_Form_Element_Text('Sop_GiroAccountNo');
        $giroAccount->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $giroAccount->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $giroAccount->setDecorators($decorator);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');

        $this->addElements(array($id, $name, $address1, $address2, $postalCode, $attention, $mainContact, $giroAccount, $submit));
    }
}