<?php
//require_once 'crud_base_config.php';
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Consts;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
use CrudBase\CrudBase;

/**
 * 基本コントローラ
 *
 */
class CrudBaseController extends Controller{
	
	
    /**
     * 初期化
     * @param [] crudBaseData
     * @return [] crudBaseData
     */
    protected function init($crudBaseData = []){
        
        global $crudBaseConfig;
        if(!empty($crudBaseConfig)){
            foreach($crudBaseConfig as $config_key => $config_value){
                $crudBaseData[$config_key] = $config_value;
            }
        }
        
        $crudBaseData['fw_type'] = $crudBaseData['fw_type'] ?? 'laravel9';
        
        // キャメル記法のモデル名をセット
        $model_name_c = $crudBaseData['model_name_c'] ?? '';
        $crudBaseData['model_name_c'] = $model_name_c;
        $crudBaseData['main_model_name_c'] = $model_name_c;
        
        // スネーク記法のモデル名
        $main_model_name_s = $crudBaseData['main_model_name_s'] ?? CrudBase::snakize($model_name_c);
        $crudBaseData['main_model_name_s'] = $main_model_name_s;
        
        // DBテーブル名
        $tbl_name = $crudBaseData['tbl_name'] ?? CrudBase::camelToTableName($model_name_c);
        $crudBaseData['tbl_name'] = $tbl_name;
        
        // デフォルトソートフィールド
        $crudBaseData['def_sort_feild'] = $crudBaseData['def_sort_feild'] ?? 'sort_no';
        
        // デフォルトソートタイプ 0:昇順 1:降順
        $crudBaseData['def_sort_type'] = $crudBaseData['def_sort_type'] ?? 0;

        // デフォルトページ情報を取得する
        $crudBaseData['defPages'] = $this->getDefPages($crudBaseData); // デフォルトページ情報を取得する

        

        return $crudBaseData;
    }
    
    /**
     * デフォルトページ情報を取得する
     * @param [] $crudBaseData
     * @return [] デフォルトページ情報
     */
    private function getDefPages(&$crudBaseData){
        
        $defPages = [];
        if(!empty($crudBaseData['defPages'])){
            $defPages = $crudBaseData['defPages'];
        }
        
        if(empty($defPages['page_no'])) $defPages['page_no'] = 0;
        if(empty($defPages['row_limit'])) $defPages['row_limit'] = 50;
        
        $def_sort_feild =  $crudBaseData['def_sort_feild']; // デフォルトソートフィールド
        $def_sort_type =  $crudBaseData['def_sort_type']; // デフォルトソートタイプ 0:昇順 1:降順
        if(empty($defPages['sort_field'])) $defPages['sort_field'] = $def_sort_feild;
        if(empty($defPages['sort_desc'])) $defPages['sort_desc'] = $def_sort_type;
        
        return $defPages;
    }
    
    
	/**
	 * ユーザー情報を取得する
	 *
	 * @return [] <mixied> ユーザー情報
	 */
	public function getUserInfo($param=[]){

		// ユーザー情報の構造
		$userInfo = [
			'id'=> 0,
			'user_id'=> 0,
		    'name' => '',
		    'username' => '',
		    'user_name' => '',
		    'update_user' => '',
			'ip_addr' => '',
			'user_agent' => '',
			'email'=>'',
			'role' => 'oparator',
			'delete_flg' => 0,
			'nickname' => '',
		    'authority_wamei'=>'',
		    'authority_name'=>'',
		    'authority_level'=>0, // 権限レベル(権限が強いほど大きな数値）
		];
		
		if(\Auth::id()){// idは未ログインである場合、nullになる。
			$userInfo['id'] = \Auth::id(); // ユーザーID
			$userInfo['user_id'] = $userInfo['id'];
			$userInfo['name'] = \Auth::user()->name; // ユーザー名
			$userInfo['username'] = $userInfo['name'] ;
			$userInfo['user_name'] = $userInfo['name'];
			$userInfo['update_user'] = $userInfo['name'];
			$userInfo['email'] = \Auth::user()->email; // メールアドレス
			$userInfo['role'] = \Auth::user()->role; // 権限
			$userInfo['nickname'] = \Auth::user()->nickname ?? $userInfo['name']; // ニックネーム
			
		}
		
		$userInfo['ip_addr'] = $_SERVER["REMOTE_ADDR"];// IPアドレス
		$userInfo['user_agent'] = $_SERVER['HTTP_USER_AGENT']; // ユーザーエージェント
		
		if(!empty($userInfo['id'])){
			$users = \DB::select("SELECT * FROM users WHERE id={$userInfo['id']}");
			$users = $users[0];
			$userInfo['role'] = $users->role;
			$userInfo['delete_flg'] = $users->delete_flg;
			
		}
		
		// 権限が空であるならオペレータ扱いにする
		if(empty($userInfo['role'])){
			$userInfo['role'] = 'oparator';
		}
		
		// 権限まわり
		$role = $userInfo['role'];
 		$userInfo['authority'] = $this->getAuthority($role);
 		$userInfo['authority_wamei'] = $userInfo['authority']['wamei'];
 		$userInfo['authority_name'] = $userInfo['authority']['name'];
 		$userInfo['authority_level'] = $userInfo['authority']['level'];
 		
		return $userInfo;
	}
	
	
	/**
	 *  レビューモード用ユーザー情報を取得
	 * @param [] $userInfo
	 * @return [] $userInfo
	 */
	public function getUserInfoForReviewMode(){
	    
	    $userInfo = $this->getUserInfo();
		
		$userInfo['id'] = -1;
		$userInfo['user_id'] = $userInfo['id'];
		$userInfo['update_user'] = 'dummy';
		$userInfo['username'] = $userInfo['update_user'];
		$userInfo['update_user'] = $userInfo['update_user'];
		$userInfo['ip_addr'] = 'dummy_ip';
		$userInfo['user_agent'] = 'dummy_user_agent';
		$userInfo['email'] = 'dummy@example.com';
		$userInfo['role'] = 'admin';
		$userInfo['delete_flg'] = 0;
		$userInfo['nickname'] = '見本ユーザー';
		$userInfo['review_mode'] = 1; // 見本モードON;
		
		$userInfo['authority'] = $this->getAuthority($role);
		$userInfo['authority_wamei'] = $userInfo['authority']['wamei'];
		$userInfo['authority_name'] = $userInfo['authority']['name'];
		$userInfo['authority_level'] = $userInfo['authority']['level'];
		
		return $userInfo;
	}
	
	/**
	 * 権限情報を取得する
	 * @return [] 権限情報
	 */
	public function getAuthorityInfo(){
	    return \App\Consts\ConstCrudBase::AUTHORITY_INFO;
	}
	

	/**
	 * 権限に紐づく権限エンティティを取得する
	 * @param string $role 権限
	 * @return array 権限エンティティ
	 */
	private function getAuthority($role){

		// 権限情報を取得する
		$authorityData = $this->getAuthorityInfo();
		
		$authority = [];
		if(!empty($authorityData[$role])){
			$authority = $authorityData[$role];
		}
		
		return $authority;
	}
	

	/**
	 * ユーザーをアプリケーションからログアウトさせる
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function logout(Request $request)
	{
	    \Auth::logout();
	    
	    $request->session()->invalidate();
	    
	    $request->session()->regenerateToken();
	    
	    return redirect('/');
	}
	
	
	/**
	 * 新バージョン判定
	 * 
	 * 	旧画面バージョンと現在の画面バージョンが一致するなら新バージョンフラグをOFFにする。
	 * 	旧画面バージョンと現在の画面バージョンが不一致なら新バージョンフラグをONにする。
	 * @param [] $sesSearches セッション検索データ
	 * @param string $this_page_version 画面バージョン
	 * @return int 新バージョンフラグ  0:バージョン変更なし（通常）, 1:新しいバージョン
	 */
	public function judgeNewVersion($sesSearches, $this_page_version){
	    
	    $old_page_version = $sesSearches['this_page_version'] ?? '';
	    $new_version = 0;
	    if($old_page_version != $this_page_version){
	        $new_version = 1;
	    }
	    return $new_version;
	}
	
	/**
	 * データをCSVファイルとしてダウンロードする。(UTF-8）
	 *
	 * @param string $csv_file CSVファイル名
	 * @param array  $data データ		エンティティ配列型
	 * @param bool $bom_flg BOMフラグ  0:BOMなし（デフォ）,  1:BOM有
	 */
	protected function csvOutput($csv_file, $data, $bom_flg=0){
	    
	    $buf = "";
	    
	    // BOM付きutf-8のファイルである場合
	    if(!empty($bom_flg)){
	        $buf = "¥xEF¥xBB¥xBF";
	    }
	    
	    // CSVデータの作成
	    if(!empty($data)){
	        $i=0;
	        foreach($data as $ent){
	            foreach($ent as $v){
	                $cell[$i][] = $v;
	            }
	            $buf .= implode(",",$cell[$i])."\n";
	            $i++;
	        }
	    }
	    
	    // CSVファイルのヘッダーを書き出す
	    header ("Content-disposition: attachment; filename=" . $csv_file);
	    header ("Content-type: application/octet-stream; name=" . $csv_file);
	    
	    print($buf); // CSVデータの書き出し
	    
	}

	
	

}