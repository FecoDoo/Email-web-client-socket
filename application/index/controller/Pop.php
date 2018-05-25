<?php
namespace app\index\controller;

class Pop
{

    public $socket;
    public $host;
    public $user;
    public $pwd;
    public $port;
    public $content;

    // 初始化
    public function __construct($host, $user, $pwd, $port = 110)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->host = $host;
        $this->user = $user;
        $this->pwd = $pwd;
        $this->port = $port;
    }

    // 收邮件
    public function pop3()
    {
        $conn = socket_connect($this->socket, $this->host, $this->port);
        if ($conn) {
            // 接收连接成功的消息
            $msg = "DCC: succeed connect to " . $this->host . ":" . $this->port . "\n";
            $msg .= $this->_r();
            // 开始认证过程
            $this->_w("USER " . $this->user . "\r\n");
            $msg .= $this->_r();
            $this->_w("PASS " . $this->pwd . "\r\n");
            $msg .= $this->_r();

            // 认证成功
            if (substr_count($msg, '+OK') >= 3) {
                //向服务器发送请求邮箱信息
                $this->_w("STAT\r\n");
                $mail_info = $this->_r();
                $msg .= $mail_info;
                preg_match('/\+OK\s(\d+)\s(\d+)/', $mail_info, $m);
                $mail_num = $m[1];
                $mail_len = $m[2];
                //向服务器发送邮件i信息的请求
                $this->content = '';
                for ($i = 1; $i <= $mail_num; $i++) {
                    // 查看该邮件的信息
                    $this->_w("LIST {$i}\r\n");
                    usleep(160000); // waiting 0.16s
                    $mail_info = $this->_r();
                    $msg .= $mail_info;

                    preg_match('/\+OK\s(\d+)\s(\d+)/', $mail_info, $m);
                    //接收服务器返回的信息
                    $this->_w("RETR {$i}\r\n");
                    $msg .= 'DCC: reading mail(id=' . $m[1] . ')\'s content(length=' . $m[2] . 'byte):' . "\n";
                    $data = '';
                    while (strlen($data) < $m[2]) {
                        $msg .= '. ';
                        $data .= socket_read($this->socket, 4096);
                    }
                    $this->content .= $data;
                    $msg .= "\nDCC: mail(id=" . $m[1] . ')\'s content(length=' . $m[2] . 'byte) read complete.' . "\n";
                }
            }
        }
        return $msg;
    }

    // 返回收取邮件内容
    public function getContent()
    {
        return $this->content;
    }

    // @发送控制流
    // var: 控制代码
    private function _w($s)
    {
        socket_write($this->socket, $s);
    }
    // @读取返回的数据
    // var: 数据长度
    private function _r($len = 1024)
    {
        return socket_read($this->socket, $len);
    }
}
