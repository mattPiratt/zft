<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DbTable.php 4246 2007-03-27 22:35:56Z ralph $
 */

/**
 * @see Zend_Layout
 */
require_once 'Zend/Layout.php';

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * @see Zend_Controller_Action_Helper_ViewRenderer
 */
require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';


/**
 * @category   Zend
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Layout_ActionHelper_LayoutManager extends Zend_Controller_Action_Helper_Abstract 
{
    
    /**
     * $_instance - singleton
     *
     * @var Zend_Layout_ActionHelper_LayoutManager
     */
    static protected $_instance         = null;
    
    /**
     * $_responseSegmentName
     *
     * @var string
     */
    protected $_responseSegmentName     = null;
    
    /**
     * getInstance() - singleton
     *
     * @return Zend_Layout_ActionHelper_LayoutManager
     */
    static public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * __construct() - enforce singleton
     *
     */
    protected function __construct()
    {
    }
    
    /**
     * setResponseSegmentName()
     *
     * @param string $name
     */
    public function setResponseSegmentName($name)
    {
        $this->_responseSegmentName = $name;
    }
    
    /**
     * init()
     *
     */
    public function init()
    {

        $view_renderer = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer');
        
        if (!$view_renderer instanceof Zend_Controller_Action_Helper_ViewRenderer) {
            throw new Zend_Layout_Exception('ViewRenderer is required to be able to use Zend_Layout');
        }
        
        if ($this->_responseSegmentName) {
            $view_renderer->setResponseSegment($this->_responseSegmentName);
        }

    }

    /**
     * useLayoutName()
     *
     * @param string $layoutName NULL (use default), FALSE (disable layouts), string (use named layout)
     * @return Zend_Layout_ActionHelper_LayoutManager
     */
    public function useLayoutName($layoutName = null)
    {
        
        switch (true) {
            
            case $layoutName === null:
                $default_name = Zend_Layout::getDefaultLayoutName();
                
                if (!$default_name) {
                    throw new Zend_Layout_Exception('LayoutManager::useLayoutName(null) was called but no default layout is set.');
                }
                
                $layout = Zend_Layout::getLayout($default_name);
                $this->_actionController->getRequest()->setParam(Zend_Layout::getParamKey(), $layout->getName());
                break;
                
            case $layoutName === false:
                $this->_actionController->getRequest()->setParam(Zend_Layout::getParamKey(), false);
                break;
                
            default:
                $this->setLayoutName($layoutName);
                break;

        }

        return $this;
    }
    
    /**
     * disableLayouts()
     *
     * @return Zend_Layout_ActionHelper_LayoutManager
     */
    public function disableLayouts()
    {
        $this->useLayoutName(false);
        return $this;
    }
    
    /**
     * setLayoutName()
     *
     * @param string $layoutName
     * @return Zend_Layout_ActionHelper_LayoutManager
     */
    public function setLayoutName($layoutName)
    {
        $layout = Zend_Layout::getLayout($layoutName);
        $this->_actionController->getRequest()->setParam(Zend_Layout::getParamKey(), $layout->getName());
        return $this;
    }
    
    /**
     * getLayout()
     *
     * @param string $layoutName
     * @param bool $createIfNotExist
     * @return Zend_Layout
     */
    public function getLayout($layoutName, $createIfNotExist = true)
    {
        $layout = Zend_Layout::getLayout($layoutName, $createIfNotExist);
        
        if (!$layout) {
            throw new Zend_Layout_Exception('Layout by the name ' . $layoutName . ' was not found, perhaps you need to configure it first.');
        }
        
        return $layout;
    }
    
    /**
     * setLayoutFile()
     *
     * @param string $file
     * @param bool $suffixIncluded
     */
    public function setLayoutFile($file, $suffixIncluded = true)
    {
        $found = false;
        $layouts = Zend_Layout::getLayouts();
        foreach ($layouts as $layout) {
            if ($layout->getFileName() == $file) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $layout_name = 'Layout' . (count($layouts)+1);
            $layout = Zend_Layout::getLayout($layout_name);
            $layout->setFileName($file, $suffixIncluded);
        }
        
        $layout->setAsDefault();
        return $this;
    }
    
    /**
     * getDefaultLayoutName()
     *
     * @return string
     */
    public function getDefaultLayoutName()
    {
        return Zend_Layout::getDefaultLayoutName();
    }
    
    /**
     * getCurrentLayoutName()
     *
     * @return string
     */
    public function getCurrentLayoutName()
    {
        $current = $this->_actionController->getRequest()->getParam(Zend_Layout::getParamKey(), null);
        
        if ($current === null) {
            return Zend_Layout::getDefaultLayoutName();
        }
        
        return $current;
    }
    
}
