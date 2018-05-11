<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Mail extends Controller
{
	public function send()
	{
		Db::connect();
		$title = $_POST['Title'];
		$receiver = $_POST['Receiver'];
		$area = $_POST['Area'];
		$smtp_port = Db::table('mailbox_info')->value('smtp_port');
		$smtp_host = Db::table('mailbox_info')->value('smtp_host');
		$mailbox = Db::table('mailbox_info')->value('mailbox');
		$passwd = Db::table('mailbox_info')->value('passwd');
		$flag = sendMail($receiver,$title,$area,$smtp_port,$smtp_host,$mailbox,$passwd);
		if($flag){
			$this->success('Successfuly sent','/dashboard');
		}else{
			$this->error('Sending failed','dashboard');
		}
	}

	public function receive()
	{
		Db::connect();
		$user = Db::table('mailbox_info')->value('user');
		$host = Db::table('mailbox_info')->value('pop_host');
		$passwd = Db::table('mailbox_info')->value('passwd');
		$pop_port = Db::table('mailbox_info')->value('pop_port');
		$flag = receiveMail($host,$user,$passwd,$pop_port);
		for($i=0; $i<=count($flag)-1; $i++ ){
			$result[$i] = explode('#', $flag[$i]); //二维数组获取字符串
			$data = [
				['title' => $result[$i][0], 'from' => $result[$i][1], 'date' => $result[$i][2], 'content' => $result[$i][3]],
			];
			Db::name('inbox')->insertAll($data);
		}


	}
}

?>