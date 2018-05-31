<?php

namespace app\index\controller;

use think\Controller;
extension_loaded("php_imap"); 
class index extends Controller
{
	public function index($password_check = 1)
	{
		// $password_check = 0;
		// $this->assign('password_check',$password_check);
		return $this->fetch('index');
	}
}