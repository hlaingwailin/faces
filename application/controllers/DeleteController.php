<?php

class DeleteController extends Zend_Controller_Action
{

    public function init()
    {
        $this->getHelper('layout')->setLayout('admin');

        if (!$this->_helper->security->isAuthenticatedAdmin()) {
            $this->_redirect('/admin/index');
        }
    }
}

?>