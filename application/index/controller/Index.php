<?php
namespace app\index\controller;
use think\Controller;

class index extends Controller
{
	public function index($password_check = 1)
	{
		// $password_check = 0;
		// $this->assign('password_check',$password_check);
		return $this->fetch('index');
	}
	public function test()
	{
		var_dump(get_extension_funcs("imap"));
	}
	public function info()
	{
		phpinfo();
	}
}