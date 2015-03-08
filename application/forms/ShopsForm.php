<?php
class Form_ShopsForm extends Zend_Form
{
    public function __construct($option = null)
    {
        parent::__construct($option);
        $this->setName('shops');
        $this->setMethod('post');
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/edit/shops');
        $decorator = array(
            array('ViewHelper')
        );

        // Tenant or Shop general info

        $id = new Zend_Form_Element_Hidden('Sho_Id');
        $id->setDecorators($decorator);

        $name = new Zend_Form_Element_Text('Sho_Name');
        $name->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Building Name');
        $name->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $name->setDecorators($decorator);

        $customerId = new Zend_Form_Element_Text('Sho_CustomerId');
        $customerId->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Address 1');
        $customerId->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $customerId->setDecorators($decorator);

        $buildingId = new Zend_Form_Element_Select('Sho_BuildingId');
        $buildingId->setAttrib('size', 1)
            ->setRequired(true)
            ->setLabel('Building Name');
        $buildingId->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;width:210px;');
        $buildingId->setDecorators($decorator);

        $shopOperatorName = new Zend_Form_Element_Text('Sop_Name');
        $shopOperatorName->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Postal Code');
        $shopOperatorName->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $shopOperatorName->setDecorators($decorator);

        $premiseAddress = new Zend_Form_Element_Text('Sho_PremiseAddress');
        $premiseAddress->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Operator Name');
        $premiseAddress->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $premiseAddress->setDecorators($decorator);

        $paymentTerm = new Zend_Form_Element_Text('Sho_PaymentTerm');
        $paymentTerm->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Operator Name');
        $paymentTerm->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $paymentTerm->setDecorators($decorator);

        $meterId = new Zend_Form_Element_Text('Sho_MeterId');
        $meterId->setAttrib('size', 30)
            ->setRequired(true)
            ->setLabel('Operator Name');
        $meterId->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');
        $meterId->setDecorators($decorator);

        $terminated = new Zend_Form_Element_Checkbox('Sho_Terminated');
        $terminated->setUncheckedValue('0');
        $terminated->setCheckedValue('1');
        $terminated->setDecorators($decorator);

        $remark = new Zend_Form_Element_Textarea('Sho_Remark');
        $remark->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;width:220px;height:70px;');
        $remark->setDecorators($decorator);


        // Deposits info

        $dep_Id = new Zend_Form_Element_Text('depositData[]');

        // Meter Reads Info



        // Invoice Info



        // Payment Info


        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');

        $this->addElements(array($id, $name, $customerId, $buildingId, $shopOperatorName, $premiseAddress, $paymentTerm, $meterId, $terminated, $remark, $submit));

    }
}