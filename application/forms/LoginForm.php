<?php
class Form_LoginForm extends Zend_Form
{
    public function __construct($option = null)
    {
        parent::__construct($option);

        $this->setName('login');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Username')
            ->setAttrib('size', 30)
            ->setRequired(true);
        $email->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
            ->setAttrib('size', 30)
            ->setRequired(true);
        $password->setAttrib('style', 'border:1px solid #AAAAAA;padding:0.3em;');

        $login = new Zend_Form_Element_Submit('login');
        $login->setLabel('Login');

        $this->addElements(array($email, $password, $login));
        $this->setMethod('post');
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/authentication/login');
    }
}