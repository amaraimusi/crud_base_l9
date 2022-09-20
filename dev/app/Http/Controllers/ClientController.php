<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;


class ClientController extends CrudBaseController{
	
	// 画面のバージョン → 開発者はこの画面を修正したらバージョンを変更すること。バージョンを変更するとキャッシュやセッションのクリアが自動的に行われます。
	public $this_page_version = '1.0.1';
	
	/**
	 * indexページのアクション
	 *
	 * @param  Request  $request
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request){

		// ログアウトになっていたらログイン画面にリダイレクト
		if(\Auth::id() == null) return redirect('login');

		// 検索データのバリデーション
		$validated = $request->validate([
			'id' => 'nullable|numeric',
			'per_page' => 'nullable|numeric',
		]);
		
		$sesSearches = session('client_searches_key');// セッションからセッション検索データを受け取る

		// セッション検索データの画面から旧画面バージョンを受け取る
		$new_version = $this->judgeNewVersion($sesSearches, $this->this_page_version);

		$searches = []; // 検索データ
		
		// リクエストのパラメータが空でない、または新バージョンフラグがONである場合、リクエストから検索データを受け取る
		if(!empty($request->all()) || $new_version == 1){
			$searches = [
				'main_search' => $request->main_search, // メイン検索
				
				// CBBXS-3000
				'id' => $request->id, // ID
				'client_name' => $request->client_name, // 顧客名
				'tell' => $request->tell, // 電話番号
				'address' => $request->address, // 住所
				'note' => $request->note, // 備考
				'delete_flg' => $request->delete_flg, // 無効フラグ
				// CBBXE
			    
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
		session(['client_searches_key' => $searches]); // セッションに検索データを書き込む

		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$model = new Client();
		$data = $model->getData($searches);

	   return view('client.index', [
			'data'=>$data,
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
		
		return view('client.create', [
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
			// CBBXS-3030
			'client_name' => 'nullable|max:200',
			'tell' => 'nullable|max:20',
			'address' => 'nullable|max:200',
			'note' => 'nullable|max:2000',
			// CBBXE
		]);
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$model = new Client();
		// CBBXS-3032
		$model->client_name = $request->client_name;
		$model->tell = $request->tell;
		$model->address = $request->address;
		$model->note = $request->note;
		// CBBXE
		
		$model->sort_no = $model->nextSortNo();
		$model->delete_flg = 0;
		$model->update_user_id = $userInfo['id'];
		$model->ip_addr = $userInfo['ip_addr'];

		$model->save();
		
		return redirect('/client');
		
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
		
		$model = new Client();
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$id = $request->id;
		if(!is_numeric($id)){
			echo 'invalid access';
			die;
		}
		
		$ent = Client::find($id);

		return view('client.show', [
			'ent'=>$ent,
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

		$model = new Client();
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$id = $request->id;
		if(!is_numeric($id)){
			echo 'invalid access';
			die;
		}
	
		$ent = Client::find($id);
		
		return view('client.edit', [
			'ent'=>$ent,
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
		   // CBBXS-3031
			'client_name' => 'nullable|max:200',
			'tell' => 'nullable|max:20',
			'address' => 'nullable|max:200',
			'note' => 'nullable|max:2000',
			// CBBXE
		]);
		
		$model = Client::find($request->id);

		$model->id = $request->id;
		
		// CBBXS-3033
		$model->client_name = $request->client_name;
		$model->tell = $request->tell;
		$model->address = $request->address;
		$model->note = $request->note;
		// CBBXE
		
		$model->delete_flg = 0;
		$model->update_user_id = $userInfo['id'];
		$model->ip_addr = $userInfo['ip_addr'];
		
 		$model->update();
		
		return redirect('/client');
		
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

		$model = Client::find($id);
		
		if(empty($action_flg)){
			$model->delete_flg = 0; // 削除フラグをOFFにする
		}else{
			$model->delete_flg = 1; // 削除フラグをONにする
		}
		
		$model->update_user_id = $userInfo['id'];
		$model->ip_addr = $userInfo['ip_addr'];
		
		$model->update();
		
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
		
		$model = new Client();
		$model->destroy($id);// idを指定して抹消（データベースかDELETE）
		
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
		
		$model = new Client();
		$model->saveAll($data);

		$res = ['success'];
		$json_str = json_encode($res);//JSONに変換
		
		return $json_str;
	}
	
	
	/**
	 * CSVダウンロード
	 *
	 * 一覧画面のCSVダウンロードボタンを押したとき、一覧データをCSVファイルとしてダウンロードします。
	 */
	public function csv_download(){
		
		// ログアウトになっていたらログイン画面にリダイレクト
		if(\Auth::id() == null) return redirect('login');

		$searches = session('client_searches_key');// セッションからセッション検索データを受け取る

		$model = new Client();
		$data = $model->getData($searches, 'csv');
		
		// データ件数が0件ならCSVダウンロードを中断し、一覧画面にリダイレクトする。
		$count = count($data);
		if($count == 0){
			return redirect('/client');
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
		$fn='client'.$strDate.'.csv';
		
		//CSVダウンロード
		$this->csvOutput($fn, $data);

	}


}