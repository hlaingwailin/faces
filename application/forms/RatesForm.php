<?php
class Form_RatesForm extends Zend_Form
{
    public function __construct($option = null)
    {
        parent::__construct($option);
        $this->setName('rates');
        $this->setMethod('post');
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/edit/meterrates');
        $decorator = array(
            array('ViewHelper')
        );

        $id = new Zend_Form_Element_Hidden('Rat_Id');
        $id->setDecorators($decorator);

        $startDate = new Zend_Form_Element_Text('Rat_StartDate');
        $startDate->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Building Name');
        $startDate->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $startDate->setDecorators($decorator);

        $endDate = new Zend_Form_Element_Text('Rat_EndDate');
        $endDate->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Building Name');
        $endDate->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $endDate->setDecorators($decorator);

        $value = new Zend_Form_Element_Text('Rat_Value');
        $value->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Address 2');
        $value->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $value->setDecorators($decorator);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');

        $this->addElements(array($id, $startDate, $endDate, $value, $submit));
    }
}