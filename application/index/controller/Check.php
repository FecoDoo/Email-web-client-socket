<?php
namespace app\index\controller;

use app\index\controller\Mail;
use think\Request;
use think\Controller;
use think\Db;

class Check extends Controller
{
	public function check_user($account = 'admin',$passwd = 'admin')
	{
		$judge = false;
		Db::connect();
		$res = Db::table('user')->where('account', $account)->value('account');
		if(!empty($res)){
			if($res == $account){
				$password = Db::table('user')->where('account', $account)->value('passwd');
				if($passwd == $password){
					$judge = true;
				}
			}
		}
		if(!$judge){
			$this->error('Password incorrect.');
		}
		return $judge;
	}

	public function login()
	{
		$account = $_POST['email'];
		$passwd = $_POST['passwd'];
		if($this->check_user($account,$passwd)){
			$this->init();
			$this->success('Success','/dashboard');
		}
	}

	public function init()
	{
		$res = new Mail();
		$res->receive();
		Db::connect();
		$number_in = Db::table('inbox')->count('title');
		Db::name('inbox_number')->where('status', 1)->update(['number_in' => $number_in]);
	}

}