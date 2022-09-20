<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmallDog;


class SmallDogController extends CrudBaseController{
	
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
		
		$sesSearches = session('small_dog_searches_key');// セッションからセッション検索データを受け取る

		// セッション検索データの画面から旧画面バージョンを受け取る
		$new_version = $this->judgeNewVersion($sesSearches, $this->this_page_version);

		$searches = []; // 検索データ
		
		// リクエストのパラメータが空でない、または新バージョンフラグがONである場合、リクエストから検索データを受け取る
		if(!empty($request->all()) || $new_version == 1){
			$searches = [
				'main_search' => $request->main_search, // メイン検索
				
				// CBBXS-3000
				'id' => $request->id, // id
				'dog_val' => $request->dog_val, // イヌ数値
				'small_dog_name' => $request->small_dog_name, // 子犬名
				'small_dog_date' => $request->small_dog_date, // 子犬日付
				'dog_type' => $request->dog_type, // 犬種
				'dog_dt' => $request->dog_dt, // 子犬保護日時
				'neko_flg' => $request->neko_flg, // ネコフラグ
				'img_fn' => $request->img_fn, // 画像ファイル名
				'note' => $request->note, // 備考
				'sort_no' => $request->sort_no, // 順番
				'delete_flg' => $request->delete_flg, // 無効フラグ
				'update_user_id' => $request->update_user_id, // 更新者
				'ip_addr' => $request->ip_addr, // IPアドレス
				'created_at' => $request->created_at, // 生成日時
				'updated_at' => $request->updated_at, // 更新日

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
		session(['small_dog_searches_key' => $searches]); // セッションに検索データを書き込む

		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$model = new SmallDog();
		$data = $model->getData($searches);
		
		// CBBXS-3020
		$dogTypeList = $model->getDogTypeList(); // 犬種

        // CBBXE
        
		return view('small_dog.index', [
		    'data'=>$data,
		    'searches'=>$searches,
		    'userInfo'=>$userInfo,
		    'this_page_version'=>$this->this_page_version,
		    
		    // CBBXS-3020B
		$dogTypeList = $model->getDogTypeList(); // 犬種

		    // CBBXE
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
		
		return view('small_dog.create', [
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
			'id' => 'nullable|numeric',
			'dog_val' => 'nullable|numeric',
	        'small_dog_name' => 'nullable|max:255',
			'small_dog_date' => 'nullable|date',
	        'img_fn' => 'nullable|max:256',
			'sort_no' => 'nullable|numeric',
			'update_user_id' => 'nullable|numeric',
	        'ip_addr' => 'nullable|max:40',

			// CBBXE
		]);
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$model = new SmallDog();
		// CBBXS-3032
		$model->dog_val = $request->dog_val; // イヌ数値
		$model->small_dog_name = $request->small_dog_name; // 子犬名
		$model->small_dog_date = $request->small_dog_date; // 子犬日付
		$model->dog_type = $request->dog_type; // 犬種
		$model->dog_dt = $request->dog_dt; // 子犬保護日時
		$model->neko_flg = $request->neko_flg; // ネコフラグ
		$model->img_fn = $request->img_fn; // 画像ファイル名
		$model->note = $request->note; // 備考

		// CBBXE
		
		$model->sort_no = $model->nextSortNo();
		$model->delete_flg = 0;
		$model->update_user_id = $userInfo['id'];
		$model->ip_addr = $userInfo['ip_addr'];

		$model->save();
		
		return redirect('/small_dog');
		
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
		
		$model = new SmallDog();
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$id = $request->id;
		if(!is_numeric($id)){
			echo 'invalid access';
			die;
		}
		
		$ent = SmallDog::find($id);

		return view('small_dog.show', [
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

		$model = new SmallDog();
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$id = $request->id;
		if(!is_numeric($id)){
			echo 'invalid access';
			die;
		}
	
		$ent = SmallDog::find($id);
		
		return view('small_dog.edit', [
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
			'id' => 'nullable|numeric',
			'dog_val' => 'nullable|numeric',
	        'small_dog_name' => 'nullable|max:255',
			'small_dog_date' => 'nullable|date',
	        'img_fn' => 'nullable|max:256',
			'sort_no' => 'nullable|numeric',
			'update_user_id' => 'nullable|numeric',
	        'ip_addr' => 'nullable|max:40',

			// CBBXE
		]);
		
		$model = SmallDog::find($request->id);

		$model->id = $request->id;
		
		// CBBXS-3033
		$model->dog_val = $request->dog_val; // イヌ数値
		$model->small_dog_name = $request->small_dog_name; // 子犬名
		$model->small_dog_date = $request->small_dog_date; // 子犬日付
		$model->dog_type = $request->dog_type; // 犬種
		$model->dog_dt = $request->dog_dt; // 子犬保護日時
		$model->neko_flg = $request->neko_flg; // ネコフラグ
		$model->img_fn = $request->img_fn; // 画像ファイル名
		$model->note = $request->note; // 備考

		// CBBXE
		
		$model->delete_flg = 0;
		$model->update_user_id = $userInfo['id'];
		$model->ip_addr = $userInfo['ip_addr'];
		
 		$model->update();
		
		return redirect('/small_dog');
		
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

		$model = SmallDog::find($id);
		
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
		
		$model = new SmallDog();
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
		
		$model = new SmallDog();
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

		$searches = session('small_dog_searches_key');// セッションからセッション検索データを受け取る

		$model = new SmallDog();
		$data = $model->getData($searches, 'csv');
		
		// データ件数が0件ならCSVダウンロードを中断し、一覧画面にリダイレクトする。
		$count = count($data);
		if($count == 0){
			return redirect('/small_dog');
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
		$fn='small_dog'.$strDate.'.csv';
		
		//CSVダウンロード
		$this->csvOutput($fn, $data);

	}


}