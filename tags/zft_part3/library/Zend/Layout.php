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
 * @version    $Id: $
 */

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * @see Zend_Layout_Request
 */
require_once 'Zend/Layout/Request.php';

/**
 * @see Zend_Layout_ActionHelper_LayoutManager
 */
require_once 'Zend/Layout/ActionHelper/LayoutManager.php';

/**
 * @category   Zend
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Layout
{
    /**
     * $_isSetup
     *
     * @var bool
     */
    static protected $_isSetup                  = false;
    
    /**
     * $_LayoutProcessor
     *
     * @var Zend_Layout_ControllerPlugin_LayoutProcessor
     */
    static protected $_layoutProcessor     = null;
    
    /**
     * $_layoutManager
     *
     * @var Zend_Layout_ActionHelper_LayoutManager
     */
    static protected $_layoutManager            = null;

    /**
     * $_path
     *
     * @var string
     */
    static protected $_path                     = null;
    
    /**
     * $_layouts
     *
     * @var Zend_Layout[]
     */
    static protected $_layouts                  = array();
    
    /**
     * $_defaultLayoutName
     *
     * @var string
     */
    static protected $_defaultLayoutName        = null;
    
    /**
     * $_paramKey
     *
     * @var string
     */
    static protected $_paramKey                 = 'layout';

    /**
     * $_userContentSegmentName
     *
     * @var string
     */
    static protected $_userContentResponseSegmentName = 'content';

    /**
     * $_fileSuffix
     *
     * @var string
     */
    static protected $_fileSuffix               = 'phtml';

    /**
     * $_view
     * 
     * @var Zend_View_Abstract
     */
    static protected $_view                     = null;
    
    /**
     * $_name
     *
     * @var string
     */
    protected $_name                            = null;
    
    /**
     * $_fileName
     *
     * @var string
     */
    protected $_fileName                        = null;
    
    /**
     * Implied Reqests
     *
     * @var Zend_Layout_Request[]
     */
    protected $_impliedRequests                 = array();

    
    /**
     * setOptions() - One stop shop for configuring the entire layout system.
     *
     * @param Zend_Config $config
     */
    static public function setOptions(Array $options)
    {
        foreach ($options as $name => $option) {
            switch ($name) {
                case 'path':
                    self::setPath($option);
                    break;
                case 'view':
                    self::setView($option);
                    break;
                default:
                    break;
            }
        }
    }
    
    /**
     * setPath()
     *
     * @param string $path
     */
    static public function setPath($path)
    {
        self::$_path = $path;
    }

    /**
     * getPath()
     *
     * @return string
     */
    static public function getPath()
    {
        return self::$_path;
    }

    /**
     * setView()
     *
     * @param Zend_View_Abstract $view
     */
    static public function setView($view)
    {
        self::$_view = $view;
    }

    /**
     * getView()
     *
     * @return Zend_View_Abstract
     */
    static public function getView()
    {
        return self::$_view;
    }
    
    /**
     * setParamKey
     *
     * @param unknown_type $key
     */
    static public function setParamKey($key)
    {
        self::$_paramKey = $key;
    }
    
    /**
     * getParamKey()
     *
     * @return string
     */
    static public function getParamKey()
    {
        return self::$_paramKey;
    }
    
    /**
     * setDefaultLayoutName()
     *
     * @param string $name
     */
    static public function setDefaultLayoutName($name)
    {
        self::$_defaultLayoutName = $name;
    }
    
    /**
     * getDefaultLayoutName()
     *
     * @return string
     */
    static public function getDefaultLayoutName()
    {
        return self::$_defaultLayoutName;
    }
    
    /**
     * setUserContentResponseSegmentName()
     *
     * @param string $name
     */
    static public function setUserContentResponseSegmentName($name)
    {
        self::$_userContentResponseSegmentName = $name;
    }
    
    /**
     * getUserContentResponseSegmentName()
     *
     * @return string
     */
    static public function getUserContentResponseSegmentName()
    {
        return self::$_userContentResponseSegmentName;
    }
    
    /**
     * setup()
     *
     * @param array $options
     * @param array $layoutSetup
     */
    static public function setup(Array $options = array(), Array $layoutSetup = array())
    {
        if ($options) {
            self::setOptions($options);
        }

        if (self::$_isSetup) {
            return;
        }

        self::$_isSetup = true;

        // make sure view renderer is in the action helper stack first
        if (!Zend_Controller_Action_HelperBroker::hasHelper('ViewRenderer')) { 
            Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer'); 
        }
        
        // get/create the helper
        if (Zend_Controller_Action_HelperBroker::hasHelper('LayoutManager')) {
            $helper = Zend_Controller_Action_HelperBroker::getExistingHelper('LayoutManager');
        } else {
            $helper = Zend_Layout_ActionHelper_LayoutManager::getInstance();
            Zend_Controller_Action_HelperBroker::addHelper($helper);
        }

        // get/create the plugin
        $controller = Zend_Controller_Front::getInstance();
        if (!($plugin = $controller->getPlugin('Zend_Layout_ControllerPlugin_LayoutProcessor'))) {
            $plugin = Zend_Layout_ControllerPlugin_LayoutProcessor::getInstance();
            $plugin->setRequest($controller->getRequest());
            $plugin->setResponse($controller->getResponse());
            $controller->registerPlugin($plugin);
        }
        
        // make plugin aware of the helper for communication
        $plugin->setLayoutManager($helper);
    }
    
    /**
     * getLayout() - get a layout by name (or create)
     *
     * @param string $layoutName
     * @return Zend_Layout
     */
    static public function getLayout($layoutName)
    {
        if (!self::$_isSetup) {
            self::setup();
        }
        
        if (isset(self::$_layouts[$layoutName])) {
            return self::$_layouts[$layoutName];
        }
        
        self::$_layouts[$layoutName] = new self($layoutName);
        
        if (self::$_defaultLayoutName == null) {
            self::setDefaultLayoutName($layoutName);
        }
        
        return self::$_layouts[$layoutName];
    }
    
    /**
     * getLayouts() 
     *
     * @return Zend_Layout[]
     */
    static public function getLayouts()
    {
        return self::$_layouts;
    }
    
    /**
     * addLayout()
     *
     * @param string $layoutName
     * @return Zend_Layout
     */
    static public function addLayout($layoutName, $fileName = null)
    {
        $layout = self::getLayout($layoutName);
        if ($fileName) {
            $layout->setFileName($fileName);
        }
    }

    /**
     * __construct() - only called from the getLayout()
     *
     * @param unknown_type $name
     */
    protected function __construct($name)
    {
        $this->_name = $name;
        $this->setFileName();
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * setFileName()
     *
     * @param string $fileName
     * @param string $suffixIncluded
     * @return Zend_Layout
     */
    public function setFileName($fileName = null, $suffixIncluded = true)
    {
        if ($fileName) {
            $this->_fileName = $fileName;
            if ($suffixIncluded) {
                $this->_fileName . '.' . self::$_fileSuffix;
            }
        } else {
            $fileName = str_replace(' ', '', $this->_name);
            $fileName = preg_replace('/([a-z])([A-Z])/', "$1-$2", $fileName);
            $this->_fileName = strtolower($fileName) . '.' . self::$_fileSuffix;
        }
        
        return $this;
    }
    
    /**
     * getFileName()
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }
    
    /**
     * addRequest
     *
     * @param Zend_Layout_Request $request
     * @return Zend_Layout
     */
    public function addRequest(Zend_Layout_Request $request)
    {
        $this->_impliedRequests[] = $request;
        return $this;
    }

    /**
     * getRequests()
     *
     * @return Zend_Layout_Request[]
     */
    public function getRequests()
    {
        return $this->_impliedRequests;
    }

    /**
     * setAsDefault()
     *
     * @param bool $isDefault
     * @return Zend_Layout
     */
    public function setAsDefault()
    {
        self::setDefaultLayoutName($this->_name);
        return $this;
    }
    
}
