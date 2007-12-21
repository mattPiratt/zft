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

		Zend_Layout::setup(array('view'	=> $this->view));
		Zend_Layout::addLayout('content','_layout.phtml');
	}
}

?>