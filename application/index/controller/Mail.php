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
        $access_code = Db::table('mailbox_info')->value('access_code');
        $user = Db::table('mailbox_info')->value('user');
        Db::name('inbox_number')->where('status', 1)->setInc('number_out', 1);
        $flag = sendMail($receiver, $title, $area, $smtp_port, $smtp_host, $mailbox, $access_code, $user);
        if ($flag) {
            $this->success('Successfuly sent', '/dashboard');
        } else {
            $this->error('Sending failed', '/dashboard');
        }
    }

    public function receive()
    {
        Db::connect();
        $user = Db::table('mailbox_info')->value('user');
        $host = Db::table('mailbox_info')->value('imap_host');
        $access_code = Db::table('mailbox_info')->value('access_code');
        $imap_port = Db::table('mailbox_info')->value('imap_port');
        $data = receiveMail($host, $user, $access_code, $imap_port);
        return;
    }
}
