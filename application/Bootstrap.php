<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoLoad()
    {
        $modelLoader = new Zend_Application_Module_Autoloader(array(
            "namespace" => '',
            "basePath" => APPLICATION_PATH));

        // get front controller instance
        $fc = Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new Plugin_CleanQuery());

        $logger = new Zend_Log();
        $writer = new Zend_Log_Writer_Firebug();
        $logger->addWriter($writer);

        Zend_Registry::set('logger', $logger);

        return $modelLoader;
    }

    function _initActionHelpers(){
        // add action helper path and namespace
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/controllers/helpers', 'My_Action_Helper');
    }

    function _initViewHelpers()
    {
        // load layout resource from resources.layout.layoutpath which is
        // defined in /config/application.ini
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->addHelperPath(APPLICATION_PATH . '/common/helpers', 'My_View_Helper');
        $view->addHelperPath(APPLICATION_PATH . '/controllers/helpers', 'My_Action_Helper');
        //$view->addHelperPath(APPLICATION_PATH .'/view/helpers', '');

        //enable Zend JQuery
        ZendX_JQuery::enableView($view);

        // use zend helpers to generate meta tags
        $view->doctype('HTML4_STRICT');
        $view->headMeta()->appendHttpEquiv('Content-type', 'text/html;charset=utf-8')
            ->appendName('description', 'FACES');

        // use zend helpers to seperate major and minor titles
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle('FACES');
    }

    function _initSetIni(){
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
    }

    function fb($message, $label = null)
    {
        if ($label != null) {
            $message = array($label, $message);
        }
        Zend_Registry::get('logger')->debug($message);
    }
}

