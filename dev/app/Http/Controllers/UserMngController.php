<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\UserMng;

class UserMngController extends CrudBaseController{
	
	// 画面のバージョン → 開発者はこの画面を修正したらバージョンを変更すること。バージョンを変更するとキャッシュやセッションのクリアが自動的に行われます。
	public $this_page_version = '1.0.2';
	

	/**
	 * indexページのアクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request){
		
		// ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');
	    $userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
	    
	    if(!$this->checkAccessByRole($userInfo)) die; // 権限によっては当画面へのアクセスを禁止する
		
		$roleData = $this->getRoleData();

		// 検索データのバリデーション
		$validated = $request->validate([
			'id' => 'nullable|numeric',
			'per_page' => 'nullable|numeric',
		]);
		
		$sesSearches = session('user_mng_searches_key');// セッションからセッション検索データを受け取る

		// セッション検索データの画面から旧画面バージョンを受け取る
		$new_version = $this->judgeNewVersion($sesSearches, $this->this_page_version);

		$searches = []; // 検索データ
		
		// リクエストのパラメータが空でない、または新バージョンフラグがONである場合、リクエストから検索データを受け取る
		if(!empty($request->all()) || $new_version == 1){
			$searches = [
				'main_search' => $request->main_search, // メイン検索
				'id' => $request->id, // ID
				'name' => $request->name, // ユーザー名/アカウント名
				'email' => $request->email, // メールアドレス
				'nickname' => $request->nickname, // 名前
				'role' => $request->role, // 権限
				'delete_flg' => $request->delete_flg, // 無効フラグ
				'update_user' => $request->update_user, // 更新者
				'sort' => $request->sort, // 並びフィールド
				'desc' => $request->desc, // 並び向き
				'per_page' => $request->per_page, // 行制限数
			];
			
		}else{
			// リクエストのパラメータが空かつ新バージョンフラグがOFFである場合、セッション検索データを検索データにセットする
			$searches = $sesSearches;
		}

		$searches['this_page_version'] = $this->this_page_version; // 画面バージョン
		$searches['new_version'] = $new_version; // 新バージョンフラグ
		session(['user_mng_searches_key' => $searches]); // セッションに検索データを書き込む

		
		$model = new UserMng();
		$roleList = $this->getRoleList($userInfo); // 権限リストを取得する
		$data = $model->getData($searches, $roleList);
		

		return view('user_mng.index', [
		    'data'=>$data,
		    'roleList'=>$roleList,
		    'searches'=>$searches,
		    'userInfo'=>$userInfo,
		    'this_page_version'=>$this->this_page_version,
		]);
		
	}
	
	
	/**
	 * 新規入力画面の表示アクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function create(Request $request){
		
		// ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');
	    $userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
	    
	    if(!$this->checkAccessByRole($userInfo)) die; // 権限によっては当画面へのアクセスを禁止する

		$model = new UserMng();
		$roleList = $this->getRoleList($userInfo); // 権限リストを取得する
		
		return view('user_mng.create', [
		    'roleList'=>$roleList,
		    'userInfo'=>$userInfo,
		    'this_page_version'=>$this->this_page_version,
		]);
		
	}
	
	
	/**
	 * 新規入力画面の登録ボタンアクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function store(Request $request){
		
		if(\Auth::id() == null) die;

		$request->validate([
			'name' => ['required', 'max:255', 'regex:/^[a-zA-Z0-9-_]+$/', 'unique:users'],
			'email' => ['required', 'email', 'max:255', 'regex:/^[a-zA-Z0-9-_.@]+$/', 'unique:users'],
			'nickname' => 'required|max:50',
			'role' => 'required|max:20',
			'password' =>['required', Password::min(8)],
			
		],[ // カスタムメッセージ。省略可能です。省略した場合は共通の定形文が使用されます。システムの利用者がよく間違えそうな入力のみ記述すると良いです。
			'name.unique'=>'このユーザー名はすでに登録されています。（既存が見当たらない場合は削除状態にあるか、上位権限者が使用している可能性があります。）',
			'name.required'=>'ユーザー名は必須入力です',
			'name.regex'=>'ユーザー名は半角英数字とハイフン( - )のみ使用できます。',
			'email.required'=>'メールアドレスは必須入力です',
			'email.regex'=>'メールアドレスに全角文字か特殊文字が混ざってます',
			'email.unique'=>'このメールアドレスはすでに登録されています。（既存が見当たらない場合は削除状態にあるか、上位権限者が使用している可能性があります。）',
		]);
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$user_mng = new UserMng();
		$user_mng->name = $request->name;
		$user_mng->email = $request->email;
		$user_mng->nickname = $request->nickname;
		$user_mng->role = $request->role;
		$user_mng->password = $request->password;
		$user_mng->sort_no = $user_mng->nextSortNo();
		$user_mng->delete_flg = 0;
		$user_mng->update_user_id = $userInfo['id'];
		$user_mng->ip_addr = $userInfo['ip_addr'];
		
		$user_mng->password = \Hash::make($user_mng->password); // パスワードをハッシュ化する。

		$user_mng->save();
		
		return redirect('/user_mng');
		
	}
	
	
	/**
	 * 詳細画面の表示アクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function show(Request $request){
		
		// ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');
	    $userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
	    
	    if(!$this->checkAccessByRole($userInfo)) die; // 権限によっては当画面へのアクセスを禁止する
		
		$model = new UserMng();
		
		$id = $request->id;
		if(!is_numeric($id)){
			echo 'invalid access';
			die;
		}
		
		$ent = UserMng::find($id);
		$roleList = $this->getRoleList($userInfo); // 権限リストを取得する

		return view('user_mng.show', [
		    'ent'=>$ent,
		    'roleList'=>$roleList,
			'userInfo'=>$userInfo,
			'this_page_version'=>$this->this_page_version,
			
		]);
		
	}
	
	
	/**
	 * 編集画面の表示アクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function edit(Request $request){
		
		// ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');
	    $userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
	    
	    if(!$this->checkAccessByRole($userInfo)) die; // 権限によっては当画面へのアクセスを禁止する
	    
		$model = new UserMng();
		
		$id = $request->id;
		if(!is_numeric($id)){
			echo 'invalid access';
			die;
		}
	
		$ent = UserMng::find($id);
		$roleList = $this->getRoleList($userInfo); // 権限リストを取得する
		
		return view('user_mng.edit', [
		    'ent'=>$ent,
		    'roleList'=>$roleList,
			'userInfo'=>$userInfo,
			'this_page_version'=>$this->this_page_version,
			
		]);
		
	}
	
	
	/**
	 * 新規入力画面の登録ボタンアクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function update(Request $request){
		
		if(\Auth::id() == null) die();

		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		
		$request->validate([
// 			'name' => 'required|max:255|regex:/^[a-zA-Z0-9-_]+$/',
// 			'nickname' => 'required|max:50',
			'role' => 'required|max:20',
			'password' =>['nullable', Password::min(8)],
			
		],[ // カスタムメッセージ。省略可能です。省略した場合は共通の定形文が使用されます。システムの利用者がよく間違えそうな入力のみ記述すると良いです。
// 			'name.required'=>'ユーザー名は必須入力です',
// 			'name.regex'=>'ユーザー名は半角英数字とハイフン( - )のみ使用できます。',
// 			'email.required'=>'メールアドレスは必須入力です',
// 			'email.regex'=>'メールアドレスに全角文字か特殊文字が混ざってます',
		]);
		
		$user_mng = UserMng::find($request->id);

		$user_mng->id = $request->id;
		$user_mng->nickname = $request->nickname;
		$user_mng->role = $request->role;
		if(!empty($request->password)){
			$user_mng->password = \Hash::make($request->password); // パスワードをハッシュ化する。
		}
		$user_mng->delete_flg = 0;
		$user_mng->update_user_id = $userInfo['id'];
		$user_mng->ip_addr = $userInfo['ip_addr'];
		
		
		
		
 		$user_mng->update();
		
		return redirect('/user_mng');
		
	}
	
	
	/**
	 * 削除/削除取消アクション(無効/有効アクション）
	 */
	public function disabled(){
		
		// ログアウトになっていたらログイン画面にリダイレクト
		if(\Auth::id() == null) return redirect('login');
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$json=$_POST['key1'];
		
		$param = json_decode($json,true);//JSON文字を配列に戻す
		$id = $param['id'];
		$action_flg =  $param['action_flg'];

		$user_mng = UserMng::find($id);
		
		if(empty($action_flg)){
			$user_mng->delete_flg = 0; // 削除フラグをOFFにする
		}else{
			$user_mng->delete_flg = 1; // 削除フラグをONにする
		}
		
		$user_mng->update_user_id = $userInfo['id'];
		$user_mng->ip_addr = $userInfo['ip_addr'];
		
		$user_mng->update();
		
		$res = ['success'];
		$json_str = json_encode($res);//JSONに変換
		
		return $json_str;
	}
	
	
	/**
	 * 抹消アクション(無効/有効アクション）
	 */
	public function destroy(){
		
		// ログアウトになっていたらログイン画面にリダイレクト
		if(\Auth::id() == null) return redirect('login');
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$json=$_POST['key1'];
		
		$param = json_decode($json,true);//JSON文字を配列に戻す
		$id = $param['id'];
		
		$user_mng = new UserMng();
		$user_mng->destroy($id);// idを指定して抹消（データベースかDELETE）
		
		$res = ['success'];
		$json_str = json_encode($res);//JSONに変換
		
		return $json_str;
	}
	
	
	/**
	 * Ajax | ソート後の自動保存
	 *
	 * @note
	 * バリデーション機能は備えていない
	 *
	 */
	public function auto_save(){
		
		// ログアウトになっていたらログイン画面にリダイレクト
		if(\Auth::id() == null) die;

		$json=$_POST['key1'];
		
		$data = json_decode($json,true);//JSON文字を配列に戻す

		$user_mng = new UserMng();
		$user_mng->saveAll($data);

		$res = ['success'];
		$json_str = json_encode($res);//JSONに変換
		
		return $json_str;
	}
	
	/**
	 * バリデーションの「attribute」を設定する。
	 * 
	 * @note
	 * 「attribute」はバリデーションのエラーメッセージで利用される。置き換え文字列のようなもの。
	 * @return string[]
	 */
	public function attributes()
	{
		return [
			'name' => 'ユーザー名',
			'email' => 'メールアドレス',
			'nickname' => '名前',
			'role' => '権限',
		];
	}
	
	/**
	 * 権限データを取得する
	 * @return [] 権限データ
	 */
	private function getRoleData(){
		return \App\Consts\ConstCrudBase::AUTHORITY_INFO;
	}
	
	/**
	 * 権限リストを取得する
	 * @return [] 権限リスト
	 */
	private function getRoleList($userInfo){
	    
	    $lu_authority_level = $userInfo['authority_level']; // ログインユーザーの権限レベルを取得
	    
	    $roleData =  \App\Consts\ConstCrudBase::AUTHORITY_INFO;
		$roleList = [];
		
		// ログインユーザーの権限未満の権限情報だけリスト化する。（上位権限の情報は除外する）
		foreach($roleData as $key =>$roleEnt){
		    if($roleEnt['level'] <  $lu_authority_level){
		        $roleList[$key] = $roleEnt['wamei'];
		    }
		}
		return $roleList;
	}
	
	/**
	 * 権限によっては当画面へのアクセスを禁止する
	 * @param [] $userInfo ユーザー情報
	 * @param bool false:アクセス禁止, true:アクセス許可
	 */
	private function checkAccessByRole($userInfo){
	    if($userInfo['authority_level'] < 11){
	        echo 'このログインユーザーの権限ではアクセスできない画面です。<br>This screen cannot be accessed with the privileges of this logged-in user.';
	        return false;
	    }
	    return true;
	}
	
	
	/**
	 * CSVダウンロード
	 *
	 * 一覧画面のCSVダウンロードボタンを押したとき、一覧データをCSVファイルとしてダウンロードします。
	 */
	public function csv_download(Request $request){
	    
	    // ログアウトになっていたらログイン画面にリダイレクト
	    if(\Auth::id() == null) return redirect('login');
	    $userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
	    
	    $str_code = $request->str_code; // 文字コード種別を取得する
	    
	    $searches = session('user_mng_searches_key');// セッションからセッション検索データを受け取る
	    
	    $model = new UserMng();
	    $roleList = $this->getRoleList($userInfo); // 権限リストを取得する
	    $data = $model->getData($searches, $roleList, 'csv');
	    
	    // データ件数が0件ならCSVダウンロードを中断し、一覧画面にリダイレクトする。
	    $count = count($data);
	    if($count == 0){
	        return redirect('/user_mng');
	    }
	    
	    // ダブルクォートで値を囲む
	    foreach($data as &$ent){
	        foreach($ent as $field => $value){
	            if(mb_strpos($value,'"')!==false){
	                $value = str_replace('"', '""', $value);
	            }
	            $value = '"' . $value . '"';
	            $ent[$field] = $value;
	        }
	    }
	    unset($ent);
	    
	    //列名配列を取得
	    $clms=array_keys($data[0]);
	    
	    //データの先頭行に列名配列を挿入
	    array_unshift($data,$clms);
	    
	    //CSVファイル名を作成
	    $date = new \DateTime();
	    $strDate=$date->format("Y-m-d");
	    
	    if(empty($str_code)){
	        
	        //CSVダウンロード
	        $fn='user_mng'.$strDate.'.csv';
	        $this->csvOutput($fn, $data);
	        
	    }elseif($str_code == 'shiftjis'){
	        
	        // Shift-Jis版CSVダウンロード
	        $fn='user_mng_sj'.$strDate.'.csv';
	        $this->csvOutputForShiftJis($fn, $data);
	        
	    }else{
	        echo 'ERROR 22072612';
	        die;
	    }
	}
	
	


}