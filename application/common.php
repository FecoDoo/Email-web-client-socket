<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use think\Db;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Db::connect();
// $pop_port = Db::table('mailbox_info')->value('pop_port');
// $pop_host = Db::table('mailbox_info')->value('pop_host');
// $smtp_port = Db::table('mailbox_info')->value('smtp_port');
// $smtp_host = Db::table('mailbox_info')->value('smtp_host');
// $user = Db::table('mailbox_info')->value('user');
// $passwd = Db::table('mailbox_info')->value('passwd');

function sendMail($to,$title,$content,$smtp_port,$smtp_host,$mailbox,$passwd){
	/*发送邮件方法
	 *@param $to：接收者 $title：标题 $content：邮件内容
	 *@return bool true:发送成功 false:发送失败
	 */
	//引入PHPMailer的核心文件使用require_once包含避免出现PHPMailer类重复定义的警告
	require_once("PHPMailer/src/phpmailer.php");
	require_once("PHPMailer/src/smtp.php");
	//实例化PHPMailer核心类
	$mail = new PHPMailer();
	//是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
	$mail->SMTPDebug = 1;
	//使用smtp鉴权方式发送邮件
	$mail->isSMTP();
	//smtp需要鉴权 这个必须是true
	$mail->SMTPAuth=true;
	//链接qq域名邮箱的服务器地址
	$mail->Host = $smtp_host;
	//设置使用ssl加密方式登录鉴权
	//$mail->SMTPSecure = 'ssl';
	//设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
	$mail->Port = $smtp_port;
	//设置smtp的helo消息头 这个可有可无 内容任意
	// $mail->Helo = 'Hello smtp.qq.com Server';
	//设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
	$mail->Hostname = 'localhost';
	//设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
	$mail->CharSet = 'UTF-8';
	//设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
	$mail->FromName = 'FecoDoo';
	//smtp登录的账号 这里填入字符串格式的qq号即可
	$mail->Username =$user;
	//smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
	$mail->Password = $passwd;
	//设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
	$mail->From = $mailbox;
	//邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
	$mail->isHTML(true);
	//设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
	$mail->addAddress($to,'Test');
	//添加多个收件人 则多次调用方法即可
	// $mail->addAddress('xxx@163.com','lsgo在线通知');
	//添加该邮件的主题
	$mail->Subject = $title;
	//添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
	$mail->Body = $content;
	//为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
	// $mail->addAttachment('./d.jpg','mm.jpg');
	//同样该方法可以多次调用 上传多个附件
	// $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');
	$status = $mail->send();
	//简单的判断与提示信息
	if($status) {
		return true;
	}else{
		return false;
	}
}

function receiveMail($host,$user,$pwd,$port)
{
	$dmail = new Pop($host, $user, $pwd, $port);
	$msg = $dmail->pop3();
	$ret = $msg;
	$mail_list = '';
	$list = array();
	if( strpos($msg, 'read complete')!==FALSE ){
		$ct = $dmail->getContent();
		$mail_list_arr = explode('+OK', $ct);
		array_shift($mail_list_arr);// 去除第一个
		foreach( $mail_list_arr as $v ){
			// 中文标题的处理
			if( preg_match('/Subject: (.*?)\s+(To)/i', $v, $subject) ){
				// $tmp_s = base64_decode($subject[2]);
				// $tmp_s = $subject[1]=='gbk'||$subject[1]=='gb2312'? iconv('gbk', 'utf-8', $tmp_s) : $tmp_s;
				$tmp_s = $subject[1];
			}
			// 英文标题
			else if(preg_match('/Subject: (.*?)\sTo/i', $v, $subject) ){
				$tmp_s = $subject[1];
			}
			else{
				$tmp_s = '邮件标题';
			}
			preg_match('/From: "=\?(.*?)\?B\?(.*?)\?="\s+<(.*?)>/i', $v, $from);
			preg_match('/Date: (.*?)\s+(From)/i', $v, $date);
			preg_match('/Date: (.*?)\s+(From)/i', $v, $date);
			preg_match('/Content-Transfer-Encoding:\sbase64\s+(.*?)\s+(\s)/i', $v, $content);
			$list[] = $tmp_s.'#'.$from[3].'#'.$date[1].'#'.$content[1];
		}
	}
	return $list;
}


class Pop{

	var $socket;
	var $host;
	var $user;
	var $pwd;
	var $port;
	var $content;

	// 初始化
	function __construct($host, $user, $pwd, $port=110){
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$this->host = $host;
		$this->user = $user;
		$this->pwd  = $pwd;
		$this->port = $port;
	}

	// 收邮件
	function pop3(){
		$conn = socket_connect($this->socket, $this->host, $this->port);
		if( $conn ){
			// 接收连接成功的消息
			$msg  = "DCC: succeed connect to ".$this->host.":".$this->port."\n";
			$msg .= $this->_r();
			// 开始认证过程
			$this->_w("USER ".$this->user."\r\n");
			$msg .= $this->_r();
			$this->_w("PASS ".$this->pwd."\r\n");
			$msg .= $this->_r();

			// 认证成功
			if( substr_count($msg, '+OK')>=3 ){
				//向服务器发送请求邮箱信息
				$this->_w("STAT\r\n");
				$mail_info = $this->_r();
				$msg .= $mail_info;
				preg_match('/\+OK\s(\d+)\s(\d+)/', $mail_info, $m);
				$mail_num = $m[1];
				$mail_len = $m[2];
				//向服务器发送邮件i信息的请求
				$this->content = '';
				for( $i=1; $i<=$mail_num; $i++ ){
					// 查看该邮件的信息
					$this->_w("LIST {$i}\r\n");
					usleep(160000);// waiting 0.16s
					$mail_info = $this->_r();
					$msg .= $mail_info;

					preg_match('/\+OK\s(\d+)\s(\d+)/', $mail_info, $m);
					//接收服务器返回的信息
					$this->_w("RETR {$i}\r\n");
					$msg.= 'DCC: reading mail(id='.$m[1].')\'s content(length='.$m[2].'byte):'."\n";
					$data = '';
					while( strlen($data)<$m[2] ){
						$msg .= '. ';
						$data .= socket_read($this->socket, 4096);
					}
					$this->content .= $data;
					$msg.= "\nDCC: mail(id=".$m[1].')\'s content(length='.$m[2].'byte) read complete.'."\n";
				}
			}
		}
		return $msg;
	}

	// 返回收取邮件内容
	function getContent(){
		return $this->content;
	}

	// @发送控制流
	// var: 控制代码
	private function _w($s){
		socket_write($this->socket, $s);
	}
	// @读取返回的数据
	// var: 数据长度
	private function _r($len=1024){
		return socket_read($this->socket, $len);
	}
}
// error_reporting(E_ERROR | E_WARNING | E_PARSE);