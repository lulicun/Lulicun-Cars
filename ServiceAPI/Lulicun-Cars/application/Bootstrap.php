<?php

//Rewrite bootstrap method, the method start with _init will be execute
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	 
	 /**
     * init memcache
     * @return Memcached
     */
    protected function _initMemcache() {
        global $memcache;
        return $memcache;
    }

    /**
     * Get configuration details
     *
     * @return Zend_Config
     */
    protected function _initConfig() {
        $memcache = $this->getResource('memcache');
        if (!$config = $memcache->get(APPLICATION_ENV . '-config')) {
            $config = new Zend_Config($this->getOptions(), true);
            $memcache->set(APPLICATION_ENV . '-config', $config);
        }
        Zend_Registry::set('config', $config);
        return $config;
    }
    
    /**
     * Initialise resources
     */
    protected function _initResourceAutoloader() {
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('Lulicun_');
        $loader->setFallbackAutoloader(true);
    }
}

