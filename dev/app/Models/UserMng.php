<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseX;

class UserMng extends BaseX
{
    protected $table = 'users'; // 紐づけるテーブル名
    
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
        'name',
        'email',
        'nickname',
        'role',
        'password',
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
     *一覧データを取得する
     * @param [] $searches 検索データ
     * @param [] $roleList 権限リスト
     * @param int $use_type 用途タイプ 　index:一覧データ用（デフォルト）, csv:CSVダウンロード用
     * @return [] 一覧データ
     */
    public function getData($searches, $roleList, $use_type='index'){
        
        // 一覧データを取得するSQLの組立。
        $query = DB::table('users')->
            leftJoin('users as LoginUser', 'users.update_user_id', '=', 'LoginUser.id');
        
        $query = $query->select(
            'users.id as id',
            'users.name as name',
            'users.email as email',
            'users.nickname as nickname',
            'users.role as role',
            'users.sort_no as sort_no',
            'users.delete_flg as delete_flg',
            'users.update_user_id as update_user_id',
            'users.ip_addr as ip_addr',
            'users.created_at as created_at',
            'users.updated_at as updated_at',
            'LoginUser.nickname as update_user',
            );
        
        // 下位権限のデータのみ取得するよう絞り込む
        $role_in_str = $this->getRoleInStr($roleList);
        $query = $query->whereRaw("users.role IN {$role_in_str}");
        
        // メイン検索
        if(!empty($searches['main_search'])){
            $concat = DB::raw("CONCAT( IFNULL(users.name, '') ,IFNULL(users.email, '') ,IFNULL(users.nickname, '')  ) ");
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
            
            $per_page = $searches['per_page'] ?? 20; // 行制限数(一覧の最大行数) デフォルトは50行まで。
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
     * 下位権限のデータのみ取得するSQLのIN句を作成する
     * @param [] $roleList 権限リスト
     */
    private function getRoleInStr($roleList){
        $keys = array_keys($roleList);
        $role_in_str = "('" . implode("','", $keys) . "')";
        return $role_in_str;
        
    }
    
    /**
     * 詳細検索情報をクエリビルダにセットする
     * @param object $query クエリビルダ
     * @param [] $searches　検索データ
     * @return object $query クエリビルダ
     */
    private function addWheres($query, $searches){
        
        if(!empty($searches['id'])){
            $query = $query->where('users.id',  $searches['id']);
        }
        
        if(!empty($searches['name'])){
            $query = $query->where('users.name', $searches['name']);
        }
        
        if(!empty($searches['email'])){
            $query = $query->where('users.email', $searches['email']);
        }
        
        if(!empty($searches['nickname'])){
            $query = $query->where('users.nickname', $searches['nickname']);
        }
        
        if(!empty($searches['role'])){
            $query = $query->where('users.role', $searches['role']);
        }
        
        // 無効フラグ
        if(!empty($searches['delete_flg'])){
            $query = $query->where('users.delete_flg',$searches['delete_flg']);
        }else{
            $query = $query->where('users.delete_flg', 0);
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
        $query = DB::table('users')->selectRaw('MAX(sort_no) AS max_sort_no');
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

