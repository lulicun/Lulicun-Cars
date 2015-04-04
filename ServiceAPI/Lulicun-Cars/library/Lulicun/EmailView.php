<?php
/**
 * This class handles basic config of the a Zend_View object to render email html
 */
class Lulicun_EmailView extends Zend_View
{
    /**
     * Zend_View config
     * 
     * @param Zend_Config $email_template_path
     */
    public function __construct() {
		$this->setScriptPath( Zend_Registry::get('config')->get('email_template_path') );
    }
}
