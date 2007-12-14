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
 * @see Zend_Layout_ActionHelper_LayoutManager
 */
require_once 'Zend/Layout/ActionHelper/LayoutManager.php';

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Layout_ControllerPlugin_LayoutProcessor extends Zend_Controller_Plugin_Abstract 
{
    /**
     * Singleton
     *
     * @var Zend_Layout_ControllerPlugin_LayoutProcessor
     */
    static protected $_instance = null;
    
    /**
     * $_inLayoutRequestLoop
     *
     * @var bool
     */
    protected $_inLayoutRequestLoop = false;
    
    /**
     * Layouts
     *
     * @var Zend_Layout
     */
    protected $_layout = null;
    
    /**
     * $_requestStack
     *
     * @var Zend_Layout_Request[]
     */
    protected $_requestStack = array();
    
    /**
     * $_currentRequest
     *
     * @var string
     */
    protected $_currentRequest = null;
    
    /**
     * $_layoutManager
     *
     * @var Zend_Layout_ActionHelper_LayoutManager
     */
    protected $_layoutManager = null;
    
    /**
     * $_responseBodyParts
     *
     * @var array|bool
     */
    protected $_responseBodyParts = false;
    
    /**
     * $_view
     *
     * @var Zend_View_Abstract
     */
    protected $_view = null;
    
    /**
     * Enter description here...
     *
     * @return Zend_Layout_ControllerPlugin_LayoutProcessor
     */
    static public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * __construct() - enforcing the singleton
     *
     */
    protected function __construct()
    {
    }
    
    /**
     * setLayoutManager()
     *
     * @param Zend_Layout_ActionHelper_LayoutManager $layoutManager
     */
    public function setLayoutManager(Zend_Layout_ActionHelper_LayoutManager $layoutManager)
    {
        $this->_layoutManager = $layoutManager;
    }
    
    /**
     * preDispatch()
     * 
     * In the even an action threw an exception and did not reset the state of dispatching,
     * this method will do cleanup since we did indeed run thinking we were in the second
     * stage of the two-step-view.
     * 
     * This assures low levels of coupling with other plugins.
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (is_array($this->_responseBodyParts)) {
            $response = $this->getResponse();
            $response->clearBody();
            foreach ($this->_responseBodyParts as $segment => $content) {
                $response->setBody($content, $segment);
            }
            
            $this->_responseBodyParts = false;
        }
    }
    
    /**
     * postDispatch()
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {

        // this module should do nothing if _forward was encountered by the last request
        if ($request->isDispatched()) {

            // initiate the Layout Request Dispatch Loop
            if (!$this->_inLayoutRequestLoop) {
                $this->_initiateLayoutRequestLoop();
            }
            
            if (count($this->_requestStack) > 0) {
                
                $current_request = array_shift($this->_requestStack);
                
                $request->setModuleName($current_request->getModuleName())
                        ->setControllerName($current_request->getControllerName())
                        ->setActionName($current_request->getActionName())
                        ->setParams($current_request->getParams())
                        ->setDispatched(false);
                
                $this->_layoutManager->setResponseSegmentName($current_request->getName());
                
            } elseif ($this->_inLayoutRequestLoop) {

                if (!$this->_view) {
                    $this->_setupView();
                }
                
                $response = $this->getResponse();
                
                if ($response->getBody() != '') {

                    $this->_responseBodyParts = $response->getBody(true);
                    foreach ($this->_responseBodyParts as $part => $content) {
                        
                        if ($part == 'default') {
                            $part = Zend_Layout::getUserContentResponseSegmentName();
                        }
                        
                        $this->_view->$part = $content;
                    }
                
                    $response->setBody($this->_view->render($this->_layout->getFileName()));
                }

            }

        }

        return;
    }
    
    /**
     * _initiateLayoutRequestLoop()
     *
     */
    protected function _initiateLayoutRequestLoop()
    {
        $request = $this->getRequest();

        if ( ($layout_name = $request->getParam(Zend_Layout::getParamKey(), null)) === null) {
            $layout_name = Zend_Layout::getDefaultLayoutName();
        }

        // if there is no layout and not default, layout is not processed
        if (!$layout_name) {
            return;
        }
        
        $this->_layout = Zend_Layout::getLayout($layout_name);
        $this->_requestStack = $this->_layout->getRequests();
        $this->_inLayoutRequestLoop = true;
        return;
    }
    
    protected function _setupView(Zend_View_Abstract $view = null)
    {
        if ($this->_view instanceof Zend_View_Abstract) {
            return;
        }
        
        if ($view === null) {
            $view = $this->_findView();
        }
        
        $this->_view = $view;
                
        $path = Zend_Layout::getPath();
        
        if ($path != '') {
            $this->_view->addBasePath($path, 'Layout_View_');
        }
        
        return;
    }
    
    protected function _findView()
    {
        
        $view = Zend_Layout::getView();
        
        if ($view instanceof Zend_View_Interface) {
            return $view;
        }
        
        if (Zend_Controller_Action_HelperBroker::hasHelper('ViewRenderer')) {
            $view_renderer = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer');
            $view_renderer->initView();
            $view = $view_renderer->view;
            if ($view instanceof Zend_View_Abstract) {
                return $view;
            }
        }

        throw new Zend_Layout_Exception('Zend_Layout could not find a Zend_View_Abstract object to utilize.');
    }
    
}
