<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\CrudBase;

class Sales extends CrudBase
{
	protected $table = 'sales'; // 紐づけるテーブル名

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	/**
	 * The attributes that are mass assignable.
     * DB保存時、ここで定義してあるDBフィールドのみ保存対象にします。
     * ここの存在しないDBフィールドは保存対象外になりますのでご注意ください。
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
	    'id',
	    'client_id',
	    'sales_amt',
	    'status',
	    'billing_date',
	    'billing_amt',
	    'payment_date',
	    'payment_amt',
	    'commission',
	    'tax',
	    'note',
	    'sort_no',
	    'delete_flg',
	    'update_user_id',
	    'ip_addr',
	    'created_at',
	    'updated_at',
	];
	
	
	public function __construct(){
	    parent::__construct();
		
	}

	/**
	 * 
	 * @param [] $searches 検索データ
	 * @param int $use_type 用途タイプ 　index:一覧データ用（デフォルト）, csv:CSVダウンロード用
	 * @return [] 一覧データ
	 */
	public function getData($searches, $use_type='index'){
	    
	    // 一覧データを取得するSQLの組立。
	    $query = DB::table('sales')->
    	    leftJoin('clients', 'sales.client_id', '=', 'clients.id')->
    	    leftJoin('users', 'sales.update_user_id', '=', 'users.id');
	    $query = $query->select(
	        'sales.id as id',
	        'sales.client_id as client_id',
	        'clients.client_name as client_name',
	        'sales.sales_amt as sales_amt',
	        'sales.status as status',
	        'sales.billing_date as billing_date',
	        'sales.billing_amt as billing_amt',
	        'sales.payment_date as payment_date',
	        'sales.payment_amt as payment_amt',
	        'sales.commission as commission',
	        'sales.tax as tax',
	        'sales.note as note',
	        'sales.sort_no as sort_no',
	        'sales.delete_flg as delete_flg',
	        'sales.update_user_id as update_user_id',
	        'sales.ip_addr as ip_addr',
	        'sales.created_at as created_at',
	        'sales.updated_at as updated_at',
	        'users.nickname as update_user',
	        );
	    
	    // メイン検索
	    if(!empty($searches['main_search'])){
	        $concat = DB::raw("CONCAT( IFNULL(clients.client_name, '') ,IFNULL(sales.note, '')) ");
	        $query = $query->where($concat, 'LIKE', "%{$searches['main_search']}%");
	    }
	    
	    $query = $this->addWheres($query, $searches); // 詳細検索情報をクエリビルダにセットする

	    $sort_field = $searches['sort'] ?? 'sort_no'; // 並びフィールド
	    $dire = 'asc'; // 並び向き
	    if(!empty($searches['desc'])){
	        $dire = 'desc';
	    }
	    $query = $query->orderBy($sort_field, $dire);
	    
	    // 一覧用のデータ取得。ページネーションを考慮している。
	    if($use_type == 'index'){
	        
	        $per_page = $searches['per_page'] ?? 10; // 行制限数(一覧の最大行数) デフォルトは50行まで。
	        $data = $query->paginate($per_page);
	        return $data;
	        
	    }
	    
	    // CSV用の出力。Limitなし
	    elseif($use_type == 'csv'){
	        $data = $query->get();
	        $data2 = [];
	        foreach($data as $ent){
	            $data2[] = (array)$ent;
	        }
	        return $data2;
	    }

	    
	}
	
	/**
	 * 詳細検索情報をクエリビルダにセットする
	 * @param object $query クエリビルダ
	 * @param [] $searches　検索データ
	 * @return object $query クエリビルダ
	 */
	private function addWheres($query, $searches){
	    
	    // ID
	    if(!empty($searches['id'])){
	        $query = $query->where('sales.id',$searches['id']);
	    }
	    
	    if(!empty($searches['sales_name'])){
	        $query = $query->where('clients.client_name', 'LIKE', "%{$searches['client_name']}%");
	    }
	    
	    // ステータス
	    if(!empty($searches['status'])){
	        $query = $query->where('sales.status',$searches['status']);
	    }
	    
	    // 請求日
	    if(!empty($searches['billing_date'])){
	        $query = $query->where('sales.billing_date',$searches['billing_date']);
	    }
	    
	    // 請求額
	    if(!empty($searches['billing_amt'])){
	        $query = $query->where('sales.billing_amt',$searches['billing_amt']);
	    }
	    
	    // 入金日
	    if(!empty($searches['payment_date'])){
	        $query = $query->where('sales.payment_date',$searches['payment_date']);
	    }
	    
	    // 入金額
	    if(!empty($searches['payment_amt'])){
	        $query = $query->where('sales.payment_amt',$searches['payment_amt']);
	    }
	    
	    // 手数料
	    if(!empty($searches['commission'])){
	        $query = $query->where('sales.commission',$searches['commission']);
	    }
	    
	    // 消費税
	    if(!empty($searches['tax'])){
	        $query = $query->where('sales.tax',$searches['tax']);
	    }
	    
	    // 備考
	    if(!empty($searches['note'])){
	        $query = $query->where('sales.note', 'LIKE', "%{$searches['note']}%");
	    }
	    
	    // 無効フラグ
	    if(!empty($searches['delete_flg'])){
	        $query = $query->where('sales.delete_flg',$searches['delete_flg']);
	    }else{
	        $query = $query->where('sales.delete_flg', 0);
	    }
	    
	    // 更新者
	    if(!empty($searches['update_user'])){
	        $query = $query->where('users.nickname',$searches['update_user']);
	    }
	    
	    return $query;
	}
	
	
	/**
	 * 次の順番を取得する
	 * @return int 順番
	 */
	public function nextSortNo(){
	    $query = DB::table('sales')->selectRaw('MAX(sort_no) AS max_sort_no');
	    $res = $query->first();
 	    $sort_no = $res->max_sort_no ?? 0;
 	    $sort_no++;

	    return $sort_no;
	}
	
	
	/**
	 * 顧客リストを取得する
	 */
	public function getClientList(){
	    $clientList = [];
	    $data = DB::table('clients')->select('id', 'client_name')->where('delete_flg',0)->get();
	    
	    foreach($data as $ent){
	        $clientList[$ent->id] = $ent->client_name; 
	    }
	    
	    return $clientList;
	}
	
	/**
	 * エンティティのDB保存
	 * @note エンティティのidが空ならINSERT, 空でないならUPDATEになる。
	 * @param [] $ent エンティティ
	 * @return [] エンティティ(insertされた場合、新idがセットされている）
	 */
	public function saveEntity(&$ent){
	    
	    foreach($ent as $field => $value){
	        if($ent[$field] === '') $ent[$field] = null;
	    }
	    
	    if(empty($ent['id'])){
	        
	        // ▽ idが空であればINSERTをする。
	        $ent = array_intersect_key($ent, array_flip($this->fillable)); // ホワイトリストによるフィルタリング
	        $id = $this->insertGetId($ent); // INSERT
	        $ent['id'] = $id;
	    }else{
	        
	        // ▽ idが空でなければUPDATEする。
	        $ent = array_intersect_key($ent, array_flip($this->fillable)); // ホワイトリストによるフィルタリング
	        $this->updateOrCreate(['id'=>$ent['id']], $ent); // UPDATE
	    }
	    
	    return $ent;
	}
	
	
	/**
	 * データのDB保存
	 * @param [] $data データ（エンティティの配列）
	 * @return [] データ(insertされた場合、新idがセットされている）
	 */
	public function saveAll(&$data){
	    
	    $data2 = [];
	    foreach($data as &$ent){
	        $data2[] = $this->saveEntity($ent);
	        
	    }
	    unset($ent);
	    return $data2;
	}
	
	
}

