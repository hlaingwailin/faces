<?php
class Zend_View_Helper_FullUrl
{
    function fullUrl($protocol='http')
    {
        if(empty($protocol)){
            $protocol = 'http';
        }
        $fc = Zend_Controller_Front::getInstance();
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $fc->getBaseUrl();
    }
}