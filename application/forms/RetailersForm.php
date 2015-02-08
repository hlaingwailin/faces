<?php
class Form_RetailersForm extends Zend_Form
{
    public function __construct($option = null)
    {
        parent::__construct($option);
        $this->setName('retailers');
        $this->setMethod('post');
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/edit/retailers');
        $decorator = array(
            array('ViewHelper')
        );

        $id = new Zend_Form_Element_Hidden('Ret_Id');
        $id->setDecorators($decorator);

        $name = new Zend_Form_Element_Text('Ret_Name');
        $name->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Building Name');
        $name->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $name->setDecorators($decorator);

        $address1 = new Zend_Form_Element_Text('Ret_Address1');
        $address1->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Address 1');
        $address1->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $address1->setDecorators($decorator);

        $address2 = new Zend_Form_Element_Text('Ret_Address2');
        $address2->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Address 2');
        $address2->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $address2->setDecorators($decorator);

        $postalCode = new Zend_Form_Element_Text('Ret_PostalCode');
        $postalCode->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $postalCode->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $postalCode->setDecorators($decorator);

        $telephone = new Zend_Form_Element_Text('Ret_Telephone');
        $telephone->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $telephone->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $telephone->setDecorators($decorator);

        $facsimile = new Zend_Form_Element_Text('Ret_Facsimile');
        $facsimile->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $facsimile->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $facsimile->setDecorators($decorator);

        $email = new Zend_Form_Element_Text('Ret_Email');
        $email->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $email->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $email->setDecorators($decorator);

        $registrationNo = new Zend_Form_Element_Text('Ret_RegistrationNo');
        $registrationNo->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $registrationNo->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $registrationNo->setDecorators($decorator);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');

        $this->addElements(array($id, $name, $address1, $address2, $postalCode, $telephone, $facsimile, $email, $registrationNo, $submit));
    }
}