<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class ui extends Controller
{

    public function dashboard($user = 'admin')
    {
        Db::connect();
        $number_in = Db::table('inbox_number')->where('status', 1)->value('number_in', 0);
        $number_out = Db::table('inbox_number')->where('status', 1)->value('number_out', 0);
        $number_ad = Db::table('inbox_number')->where('status', 1)->value('number_ad', 0);
        $number_trash = Db::table('inbox_number')->where('status', 1)->value('number_trash', 0);
        $this->assign([
            'number_in' => $number_in,
            'number_out' => $number_out,
            'number_ad' => $number_ad,
            'number_trash' => $number_trash,
        ]);
        return $this->fetch('dashboard');
    }
    function empty() {
        return $this->fetch('empty');
    }
    public function send()
    {
        return $this->fetch('send');
    }
    public function inbox()
    {
        Db::connect();
        $count = Db::name('inbox')->count();
        $subject = Db::name('inbox')->column('subject');
        $from = Db::name('inbox')->column('from');
        $date = Db::name('inbox')->column('date');
        $content = Db::name('inbox')->column('content');
        $list = [];
        for ($i = 0; $i < $count; $i++) {
            $mail = [
                'subject' => $subject[$i],
                'from' => $from[$i],
                'date' => $date[$i],
                'content' => $content[$i],
            ];
            array_push($list, $mail);
        }
        $this->assign('list', $list);
        return $this->fetch('inbox');
    }
}
