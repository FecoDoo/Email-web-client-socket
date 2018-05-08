<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Mail extends Controller
{
	public function send()
	{
		$title = $_POST['Title'];
		$receiver = $_POST['Receiver'];
		$area = $_POST['Area'];
		$flag = sendMail($receiver,$title,$area);
		$this->success('Successfuly sent','/dashboard');
	}
	public function receive()
	{
		Db::connect();
		$user = Db::table('info')->value('user');
		$host = Db::table('info')->value('host');
		$passwd = Db::table('info')->value('passwd');
		$flag = receiveMail($host,$user,$passwd);
		for($i=0; $i<=count($flag)-1; $i++ ){
			$result[$i] = explode('#', $flag[$i]); //二维数组获取字符串
			var_dump($result[$i][0]        );
			$data = [
				['title' => $result[$i][0], 'from' => $result[$i][1], 'date' => $result[$i][2]]
			];
			Db::name('inbox')->insertAll($data);
		}


	}
}

?>