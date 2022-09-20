<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales;

class SalesController extends CrudBaseController{
	
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
			'billing_date' => 'nullable|date',
			'billing_amt' => 'nullable|numeric',
			'payment_date' => 'nullable|date',
			'payment_amt' => 'nullable|numeric',
			'commission' => 'nullable|numeric',
			'tax' => 'nullable|numeric',
			'per_page' => 'nullable|numeric',
			
		]);
		
		$sesSearches = session('sales_searches_key');// セッションからセッション検索データを受け取る

		// セッション検索データの画面から旧画面バージョンを受け取る
		$new_version = $this->judgeNewVersion($sesSearches, $this->this_page_version);

		$searches = []; // 検索データ
		
		// リクエストのパラメータが空でない、または新バージョンフラグがONである場合、リクエストから検索データを受け取る
		if(!empty($request->all()) || $new_version == 1){
			
			$searches = [
				'main_search' => $request->main_search, // メイン検索
				
				// CBBXS-3000
			    'id' => $request->id, /// ID
				'client_name' => $request->client_name, // 顧客名
				'status' => $request->status, // ステータス
				'billing_date' => $request->billing_date, // 請求日
				'billing_amt' => $request->billing_amt, // 請求額
				'payment_date' => $request->payment_date, // 入金日
				'payment_amt' => $request->payment_amt, // 入金額
				'commission' => $request->commission, // 手数料
				'tax' => $request->tax, // 消費税
				'note' => $request->note, // 備考
				'delete_flg' => $request->delete_flg, // 無効フラグ
				'update_user' => $request->update_user, // 更新者
				// CBBXE
				
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
		session(['sales_searches_key' => $searches]); // セッションに検索データを書き込む

		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$model = new Sales();
		$data = $model->getData($searches);

		$salesStatusList = $this->getSalesStatusList(); // 売上ステータスリスト

	   return view('sales.index', [
			'data'=>$data,
			'searches'=>$searches,
			'userInfo'=>$userInfo,
			'salesStatusList'=>$salesStatusList,
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
		
		$model = new Sales();
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$clientList = $model->getClientList(); // 顧客リストを取得
		$salesStatusList = $this->getSalesStatusList(); // 売上ステータスリスト

		return view('sales.create', [
			'clientList'=>$clientList,
			'salesStatusList'=>$salesStatusList,
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
			'sales_amt' => 'required|numeric',
			'billing_date' => 'nullable|date',
			'billing_amt' => 'nullable|numeric',
			'payment_date' => 'nullable|date',
			'payment_amt' => 'nullable|numeric',
			'commission' => 'nullable|numeric',
			'tax' => 'nullable|numeric',
			'note' => 'nullable|max:2000',
			
		]);
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$sales = new Sales();
		$sales->client_id = $request->client_id;
		$sales->sales_amt = $request->sales_amt;
		$sales->status = $request->status;
		$sales->billing_date = $request->billing_date ?? null;
		$sales->billing_amt = $request->billing_amt;
		$sales->payment_date = $request->payment_date ?? null;
		$sales->payment_amt = $request->payment_amt;
		$sales->commission = $request->commission;
		$sales->tax = $request->tax;
		$sales->note = $request->note;
		$sales->sort_no = $sales->nextSortNo();
		$sales->delete_flg = 0;
		$sales->update_user_id = $userInfo['id'];
		$sales->ip_addr = $userInfo['ip_addr'];

		$sales->save();
		
		return redirect('/sales');
		
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
	    
	    $model = new Sales();
	    $userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
	    
	    $id = $request->id;
	    if(!is_numeric($id)){
	        echo 'invalid access';
	        die;
	    }
	    
	    $ent = Sales::find($id);
	    
	    $clientList = $model->getClientList(); // 顧客リストを取得
	    $salesStatusList = $this->getSalesStatusList(); // 売上ステータスリスト
	    
	    return view('sales.show', [
	        'ent'=>$ent,
	        'clientList'=>$clientList,
	        'salesStatusList'=>$salesStatusList,
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

		$model = new Sales();
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$id = $request->id;
		if(!is_numeric($id)){
			echo 'invalid access';
			die;
		}
	
		$ent = Sales::find($id);
		
		$clientList = $model->getClientList(); // 顧客リストを取得
		$salesStatusList = $this->getSalesStatusList(); // 売上ステータスリスト
		
		return view('sales.edit', [
			'ent'=>$ent,
			'clientList'=>$clientList,
			'salesStatusList'=>$salesStatusList,
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
			'sales_amt' => 'required|numeric',
			'billing_date' => 'nullable|date',
			'billing_amt' => 'nullable|numeric',
			'payment_date' => 'nullable|date',
			'payment_amt' => 'nullable|numeric',
			'commission' => 'nullable|numeric',
			'tax' => 'nullable|numeric',
			'note' => 'nullable|max:2000',
			
		]);
		
		$sales = Sales::find($request->id);

		$sales->id = $request->id;
		$sales->client_id = $request->client_id;
		$sales->sales_amt = $request->sales_amt;
		$sales->status = $request->status;
		$sales->billing_date = $request->billing_date ?? null;
		$sales->billing_amt = $request->billing_amt;
		$sales->payment_date = $request->payment_date ?? null;
		$sales->payment_amt = $request->payment_amt;
		$sales->commission = $request->commission;
		$sales->tax = $request->tax;
		$sales->note = $request->note;
		$sales->update_user_id = $userInfo['id'];
		$sales->ip_addr = $userInfo['ip_addr'];
		
 		$sales->update();
		
		return redirect('/sales');
		
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

	    $sales = Sales::find($id);
	    
	    if(empty($action_flg)){
	        $sales->delete_flg = 0; // 削除フラグをOFFにする
	    }else{
	        $sales->delete_flg = 1; // 削除フラグをONにする
	    }
	    
	    $sales->update_user_id = $userInfo['id'];
	    $sales->ip_addr = $userInfo['ip_addr'];
	    
	    $sales->update();
	    
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
	    
	    $sales = new Sales();
	    $sales->destroy($id);// idを指定して抹消（データベースかDELETE）
	    
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
		
		$sales = new Sales();
		$sales->saveAll($data);

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

		$searches = session('sales_searches_key');// セッションからセッション検索データを受け取る

		$model = new Sales();
		$data = $model->getData($searches, 'csv');
		
		// データ件数が0件ならCSVダウンロードを中断し、一覧画面にリダイレクトする。
		$count = count($data);
		if($count == 0){
		    return redirect('/sales');
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
		$fn='sales_mng'.$strDate.'.csv';
		
		//CSVダウンロード
		$this->csvOutput($fn, $data);

	}

	/**
	 * 売上ステータスリストを取得する
	 * @return [] 売上ステータスリスト
	 */
	private function getSalesStatusList(){
		return \App\Consts\ConstSales::SAILES_STATUS_LIST;
	}

}