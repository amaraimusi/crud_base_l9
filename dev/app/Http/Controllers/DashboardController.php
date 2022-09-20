<?php

namespace App\Http\Controllers;
use App\Http\Controllers\CrudBaseController;
use Illuminate\Http\Request;
use Illuminate¥Support¥Facades¥DB;

class DashboardController extends CrudBaseController
{
	
	// 当画面のバージョン（バージョンを変更するとjs, css読込のキャッシュ読込対策が行われる）
	public $this_page_version = '1.0.0';
	

	public function index(){
		
	    // ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null){
	        return redirect('login');
	    }
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する

		return view('dashboard.index', [
		    'userInfo' => $userInfo,
		    'this_page_version' => $this->this_page_version,
		]);
		
		
	}

}


