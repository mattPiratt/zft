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
 * @see Zend_Controller_Request_Abstract
 */
require_once 'Zend/Controller/Request/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Layout_Request extends Zend_Controller_Request_Abstract 
{
    /**
     * $_name
     *
     * @var string
     */
    protected $_name = null;
    
    /**
     * __construct()
     *
     * @param string $name
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     */
    public function __construct($name, $action, $controller, $module = 'default', Array $params = array())
    {
        $this->setName($name)
             ->setActionName($action)
             ->setControllerName($controller)
             ->setModuleName($module)
             ->setParams($params);
    }
    
    /**
     * setName()
     *
     * @param string $name
     * @return Zend_Layout_Request
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
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
    
    
}