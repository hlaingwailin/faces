<?php
class Form_BuildingOperatorsForm extends Zend_Form
{
    public function __construct($option = null)
    {
        parent::__construct($option);
        $this->setName('buildingoperator');
        $this->setMethod('post');
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/edit/buildingoperators');
        $decorator = array(
            array('ViewHelper')
        );

        $id = new Zend_Form_Element_Hidden('Buo_Id');
        $id->setDecorators($decorator);

        $name = new Zend_Form_Element_Text('Buo_Name');
        $name->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Building Name');
        $name->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $name->setDecorators($decorator);

        $address1 = new Zend_Form_Element_Text('Buo_Address1');
        $address1->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Address 1');
        $address1->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $address1->setDecorators($decorator);

        $address2 = new Zend_Form_Element_Text('Buo_Address2');
        $address2->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Address 2');
        $address2->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $address2->setDecorators($decorator);

        $postalCode = new Zend_Form_Element_Text('Buo_PostalCode');
        $postalCode->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $postalCode->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $postalCode->setDecorators($decorator);

        $registrationNo = new Zend_Form_Element_Text('Buo_RegistrationNo');
        $registrationNo->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $registrationNo->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $registrationNo->setDecorators($decorator);

        $gstRegistrationNo = new Zend_Form_Element_Text('Buo_GSTRegistrationNo');
        $gstRegistrationNo->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $gstRegistrationNo->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $gstRegistrationNo->setDecorators($decorator);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');

        $this->addElements(array($id, $name, $address1, $address2, $postalCode, $registrationNo, $gstRegistrationNo, $submit));
    }
}