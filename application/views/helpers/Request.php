<?php
class Zend_View_Helper_Request
{
    private $front;

    function request(){

        $this->front = Zend_Controller_Front::getInstance();
        return $this;
    }

    function getControllerName(){
        $currControllerName = $this->front->getRequest()->getControllerName();
        return $currControllerName;
    }

    function getActionName(){
        $currActionName = $this->front->getRequest()->getActionName();
        return $currActionName;
    }

    function getAllParams(){
        $params = $this->front->getRequest()->getParams();
        return $params;
    }

}