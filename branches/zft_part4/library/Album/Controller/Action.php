<?php
abstract class Album_Controller_Action extends Zend_Controller_Action {

	protected $obConfig;

	public function init() {
		// load configuration
		$this->obConfig = new Zend_Config_Ini('../application/config.ini', 'general');

		set_include_path('.' . PATH_SEPARATOR . $this->obConfig->path->models
			. PATH_SEPARATOR . get_include_path());

		// setup database
		$db = Zend_Db::factory(
			$this->obConfig->db->adapter,
			$this->obConfig->db->config->toArray() );
		Zend_Db_Table::setDefaultAdapter($db);

		//setup view heplerer
		$view = new Album_View_Smarty();
		$view->setScriptPath($this->obConfig->smarty->template_dir);
		$view->setCompilePath($this->obConfig->smarty->compile_dir);
		$view->setCachePath($this->obConfig->smarty->cache_dir);
		$view->setConfigPath($this->obConfig->smarty->config_dir);
		$this->view = $view;

		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer
			->setView($view)
			->setViewSuffix('tpl');

		//use layout view pattern
		$layout = Zend_Layout::startMvc();
		$layout->setViewSuffix('tpl');
		$view->assign( 'layout', $layout );

	}
}

?>