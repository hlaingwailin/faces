<?php

/**
 * Plugin that cleans up querystrings in GET submissions
 */
class Plugin_CleanQuery extends Zend_Controller_Plugin_Abstract
{


    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        if (count($request->getQuery())) {
                $router = Zend_Controller_Front::getInstance()->getRouter();
                $url = $router->assemble(array_reverse($request->getQuery(), true), null, false, true);

                $this->getResponse()->setRedirect($url)->sendResponse();
                exit;
            }
        }
}
