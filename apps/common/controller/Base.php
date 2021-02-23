<?php

namespace app\common\controller;
use think\Controller;
use think\Cookie;
use eacoo\EacooAccredit;

class Base extends Controller {

	protected $url;
	protected $request;
	protected $module;
	protected $controller;
	protected $action;

	public function _initialize() {
		defined('ACCREDIT_TOKEN') or define('ACCREDIT_TOKEN',EacooAccredit::getAccreditToken());//获取本地授权token
		//获取request信息
		$this->requestInfo();
		//halt(config());
	}

	/**
	 * request信息
	 * @return [type] [description]
	 */
	protected function requestInfo() {
		
		defined('MODULE_NAME') or define('MODULE_NAME', $this->request->module());
		defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $this->request->controller());
		defined('ACTION_NAME') or define('ACTION_NAME', $this->request->action());
		defined('IS_POST') or define('IS_POST', $this->request->isPost());
		defined('IS_AJAX') or define('IS_AJAX', $this->request->isAjax());
		defined('IS_PJAX') or define('IS_PJAX', $this->request->isPjax());
		defined('IS_GET') or define('IS_GET', $this->request->isGet());

		//$this->param = $this->request->param();
		$this->urlRule = strtolower($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
		$this->ip = $this->request->ip();
		$this->url = $this->request->url(true);//完整url
	}
    
}
