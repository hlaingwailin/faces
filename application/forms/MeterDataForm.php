<?php

class Form_MeterDataForm extends Zend_Form
{

    public function __construct($option = null)
    {
        parent::__construct($option);
        $this->setName('meterdata');
        $this->setMethod('post');
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/edit/meterdata');
        $decorator = array(
            array('ViewHelper')
        );

        $id = new Zend_Form_Element_Hidden('Met_Id');
        $id->setDecorators($decorator);

        $meterSerialNumber = new Zend_Form_Element_Text('Met_MeterSerialNumber');
        $meterSerialNumber->setAttrib('size', 30)
            ->setAttrib('readonly', 'readonly')
            ->setAttrib('class', 'inputReadonly')
            ->setRequired(true)
            ->setLabel('Meter Serial Number');
        $meterSerialNumber->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $meterSerialNumber->setDecorators($decorator);

        $shopName = new Zend_Form_Element_Text('Sho_Name');
        $shopName->setAttrib('size', 30)
            ->setAttrib('readonly', 'readonly')
            ->setAttrib('class', 'inputReadonly')
            ->setRequired(true)
            ->setLabel('Shop Name');
        $shopName->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $shopName->setDecorators($decorator);

        $lastReadUsage = new Zend_Form_Element_Text('Met_LastReadUsage');
        $lastReadUsage->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Last Read Usage');
        $lastReadUsage->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $lastReadUsage->setDecorators($decorator);

        $lastReadDate = new Zend_Form_Element_Text('Met_LastReadDate');
        $lastReadDate->setAttrib('size', 30)
            ->setAttrib('readonly', 'readonly')
            ->setAttrib('class', 'inputReadonly')
            ->setRequired(true)
            ->setLabel('Last Read Date');
        $lastReadDate->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $lastReadDate->setDecorators($decorator);

        $currentReadUsage = new Zend_Form_Element_Text('Met_CurrentReadUsage');
        $currentReadUsage->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Current Read Usage');
        $currentReadUsage->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $currentReadUsage->setDecorators($decorator);

        $currentReadDate = new Zend_Form_Element_Text('Met_CurrentReadDate');
        $currentReadDate->setAttrib('size', 30)
            ->setAttrib('readonly', 'readonly')
            ->setAttrib('class', 'inputReadonly')
            ->setRequired(true)
            ->setLabel('Current Read Date');
        $currentReadDate->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $currentReadDate->setDecorators($decorator);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');

        $this->addElements(array($id, $meterSerialNumber, $shopName, $lastReadUsage, $lastReadDate, $currentReadUsage, $currentReadDate, $submit));

    }
}

?>