<?php
namespace app\index\controller;

use app\index\controller\Mail;
use think\Controller;

class Sync extends Controller
{
	public function sync()
    {
    	$this->redirect('/ui/inbox');
    }  
}