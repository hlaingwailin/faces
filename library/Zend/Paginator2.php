<?php

/**
 * Zend_Paginator2 fixed the cache reading problem in Zend_Paginator
 * 
 * The cache is created with multiple IDs and not read correctly in default 
 * implementation of Zend_Paginator. This class fixes it.
 * 
 * The default Zend_Paginator is assuming that adapter will not mutate, which
 * is not always true. As the adapter mutates, serialization of adapter results 
 * in different serialized strings and different cache IDs 
 */
class Zend_Paginator2 extends Zend_Paginator
{
    protected $_adapterSerialization = null;
    
    /**
     * Constructor.
     *
     * @param Zend_Paginator_Adapter_Interface|Zend_Paginator_AdapterAggregate $adapter
     */
    public function __construct($adapter)
    {
    	parent::__construct($adapter);     	
		$this->_adapterSerialization = serialize($adapter);		
    }
   
    protected function _getCacheInternalId()
    {
        return md5($this->_adapterSerialization . $this->getItemCountPerPage());
    }

}
