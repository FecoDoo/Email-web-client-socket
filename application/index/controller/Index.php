<?php
namespace app\index\controller;
use think\Controller;

class index extends Controller
{
	public function index($password_check = 1)
	{
		$this->assign(['password_check' => $password_check]);
		return $this->fetch('index');
	}
}