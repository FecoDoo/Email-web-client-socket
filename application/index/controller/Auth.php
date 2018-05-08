<?php

classPublicActionextendsCommonAction{


	//验证是否账号密码
	function checklogin(){
	//此处多余可自行改为Model自动验证
		if(empty($_POST['username'])){
			$this->error('帐号错误!');
		}elseif (empty($_POST['password'])){
			$this->error('密码必须!');
		}
		$map=array();
		$map['username']=$_POST['username'];
		$map['status']=array('gt',0);

		import('ORG.Util.RBAC');
		//C('USER_AUTH_MODEL','User');
		//验证账号密码
		$authInfo=RBAC::authenticate($map);
		if(empty($authInfo)){
			$this->error('账号不存在或者被禁用!');
		}else{
			if($authInfo['password']!=md5($_POST['password'])){
				$this->error('账号密码错误!');
			}else{
				$_SESSION[C('USER_AUTH_KEY')]=$authInfo['id'];//记录认证标记,必须有。其他信息根据情况取用。
				$_SESSION['email']=$authInfo['email'];
				$_SESSION['nickname']=$authInfo['nickname'];
				$_SESSION['user']=$authInfo['username'];
				$_SESSION['last_login_date']=$authInfo['last_login_date'];
				$_SESSION['last_login_ip']=$authInfo['last_login_ip'];
				//判断是否为超级管理员
				if($authInfo['username']=='admin'){
					$_SESSION[C('ADMIN_AUTH_KEY')]=true;
				}
				//以下操作为记录本次登录信息
				$user=M('User');
				$lastdate=date('Y-m-d H:i:s');
				$data=array();
				$data['id']=$authInfo['id'];
				$data['last_login_date']=$lastdate;
				$data['last_login_ip']=$_SERVER["REMOTE_ADDR"];
				$user->save($data);
				RBAC::saveAccessList();//用于检测用户权限的方法,并保存到Session中
				$this->assign('jumpUrl',.'/Index/index');
				$this->success('登录成功!');
			}
		}
	}


	//退出登录操作
	function logout(){
		if(!empty($_SESSION[C('USER_AUTH_KEY')])){
			unset($_SESSION[C('USER_AUTH_KEY')]);
			$_SESSION=array();
			session_destroy();
			$this->assign('jumpUrl',/Code.'/login');
			$this->success('登出成功');
		}else{
			$this->error('已经登出了');
		}
	}
}