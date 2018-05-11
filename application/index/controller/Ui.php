<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\controller\Mail;

class ui extends Controller
{

	public function dashboard($user = 'admin')
	{
		Db::connect();
		$number_in = Db::table('inbox_number')->where('status',1)->value('number_in',0);
		$number_out = Db::table('inbox_number')->where('status',1)->value('number_out',0);
		$number_ad = Db::table('inbox_number')->where('status',1)->value('number_ad',0);
		$number_trash = Db::table('inbox_number')->where('status',1)->value('number_trash',0);
		$this->assign([
			'number_in' => $number_in,
			'number_out' => $number_out,
			'number_ad' => $number_ad,
			'number_trash' => $number_trash,
		]);
		return $this->fetch('dashboard');
	}
	public function empty()
	{
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
		$title = Db::name('inbox')->column('title');
		$from = Db::name('inbox')->column('from');
		$date = Db::name('inbox')->column('date');
		$this->assign([
			'title0' => $title[0],
			'title1' => $title[1],
			// 'title2' => $title[2],
			// 'title3' => $title[3],
			'from0' => $from[0],
			'from1' => $from[1],
			// 'from2' => $from[2],
			// 'from3' => $from[3],
			'date0' => $date[0],
			'date1' => $date[1],
			// 'date2' => $date[2],
			// 'date3' => $date[3],
		]);
		return $this->fetch('inbox');
	}
}