<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\CrudBase;

class BigCat extends CrudBase
{
	protected $table = 'big_cats'; // 紐づけるテーブル名
	
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
		// CBBXS-3009
		'id',
	    'big_cat_name', // ネコ名
	    'public_date', // 公開日
	    'big_cat_type', // 有名猫種別
	    'price', // 価格
	    'subsc count', // サブスク数
	    'work_dt', // 作業日時
	    'big_cat_flg', // ネコフラグ
	    'img_fn', // 画像ファイル名
	    'note', // 備考
		'sort_no',
		'delete_flg',
		'update_user_id',
		'ip_addr',
		'created_at',
		'updated_at',

		// CBBXE
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
		$query = DB::table('big_cats')->
			leftJoin('users', 'big_cats.update_user_id', '=', 'users.id');
		
		$query = $query->select(
		    // CBBXS-3034
		    'big_cats.id as id',
		    'big_cats.big_cat_name as big_cat_name', // ネコ名
		    'big_cats.public_date as public_date', // 公開日
		    'big_cats.big_cat_type as big_cat_type', // 有名猫種別
		    'big_cats.price as price', // 価格
		    'big_cats.subsc_count as subsc count', // サブスク数
		    'big_cats.work_dt as work_dt', // 作業日時
		    'big_cats.big_cat_flg as big_cat_flg', // ネコフラグ
		    'big_cats.img_fn as img_fn', // 画像ファイル名
		    'big_cats.note as note', // 備考
			'big_cats.sort_no as sort_no',
			'big_cats.delete_flg as delete_flg',
		    'big_cats.update_user_id as update_user_id',
		    'users.nickname as update_user',
			'big_cats.ip_addr as ip_addr',
			'big_cats.created_at as created_at',
			'big_cats.updated_at as updated_at',

		    // CBBXE
			);
		
		// メイン検索
		if(!empty($searches['main_search'])){
			$concat = DB::raw("CONCAT( IFNULL(big_cats.big_cat_name, '') , IFNULL(big_cats.note, '') ) ");
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
			
			$per_page = $searches['per_page'] ?? 50; // 行制限数(一覧の最大行数) デフォルトは50行まで。
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
		
	    // CBBXS-3003

	    // id
	    if(!empty($searches['id'])){
	        $query = $query->where('big_cats.id',$searches['id']);
	    }
	    
	    // ネコ名
	    if(!empty($searches['big_cat_name'])){
	        $query = $query->where('big_cats.big_cat_name', 'LIKE', "%{$searches['big_cat_name']}%");
	    }
	    
	    // 公開日
	    if(!empty($searches['public_date'])){
	        $query = $query->where('big_cats.public_date',$searches['public_date']);
	    }
	    
	    // 猫種別
	    if(!empty($searches['big_cat_type'])){
	        $query = $query->where('big_cats.big_cat_type',$searches['big_cat_type']);
	    }
	    
	    // 価格
	    if(!empty($searches['price'])){
	        $query = $query->where('big_cats.price',$searches['price']);
	    }
	    
	    // サブスク数
	    if(!empty($searches['subsc_count'])){
	        $query = $query->where('big_cats.subsc_count',$searches['subsc_count']);
	    }

	    // 作業日時
	    if(!empty($searches['work_dt'])){
	        $query = $query->where('big_cats.work_dt',$searches['work_dt']);
	    }

	    // ネコフラグ
	    if(isset($searches['big_cat_flg'])){
	        $query = $query->where('big_cats.big_cat_flg',$searches['big_cat_flg']);
	    }

	    // 画像ファイル名
	    if(!empty($searches['img_fn'])){
	        $query = $query->where('big_cats.img_fn', 'LIKE', "%{$searches['img_fn']}%");
	    }

	    // 備考
	    if(!empty($searches['note'])){
	        $query = $query->where('big_cats.note', 'LIKE', "%{$searches['note']}%");
	    }

	    // 順番
	    if(!empty($searches['sort_no'])){
	        $query = $query->where('big_cats.sort_no',$searches['sort_no']);
	    }

	    // 無効フラグ
	    if(!empty($searches['delete_flg'])){
	        $query = $query->where('big_cats.delete_flg',$searches['delete_flg']);
	    }else{
	        $query = $query->where('big_cats.delete_flg', 0);
	    }

	    // 更新者
	    if(!empty($searches['update_user'])){
	        $query = $query->where('users.nickname',$searches['update_user']);
	    }

	    // IPアドレス
	    if(!empty($searches['ip_addr'])){
	        $query = $query->where('big_cats.ip_addr', 'LIKE', "%{$searches['ip_addr']}%");
	    }

	    // 生成日時
	    if(!empty($searches['created_at'])){
	        $query = $query->where('big_cats.created_at',$searches['created_at']);
	    }

	    // 更新日
	    if(!empty($searches['updated_at'])){
	        $query = $query->where('big_cats.updated_at',$searches['updated_at']);
	    }

		// CBBXE
		
		return $query;
	}
	
	
	/**
	 * 次の順番を取得する
	 * @return int 順番
	 */
	public function nextSortNo(){
		$query = DB::table('big_cats')->selectRaw('MAX(sort_no) AS max_sort_no');
		$res = $query->first();
		$sort_no = $res->max_sort_no ?? 0;
		$sort_no++;
		
		return $sort_no;
	}
	
	
	/**
	 * エンティティのDB保存
	 * @note エンティティのidが空ならINSERT, 空でないならUPDATEになる。
	 * @param [] $ent エンティティ
	 * @return [] エンティティ(insertされた場合、新idがセットされている）
	 */
	public function saveEntity(&$ent){
		
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
	
	// CBBXS-3021
	/**
	 *  有名猫種別リストを取得する
	 *  @return [] 有名猫種別リスト
	 */
	public function getBigCatTypeList(){
	    
	    $query = DB::table('big_cat_types')->
	       select(['id', 'big_cat_type_name'])->
	       where('delete_flg',0);
	    
	    $res = $query->get();
	    $list = [];
	    foreach($res as $ent){
	        $list[$ent->id] = $ent->big_cat_type_name;
	    }

	    return $list;
	}
	// CBBXE
	
}

