<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * モデルクラスのベースクラス
 * 
 * @desc 各管理画面のモデルで共通するメソッドを記述する。
 * @version 1.0.0
 * @since 2022-7-4
 * @author kenji uehara
 *
 */
class CrudBase extends Model{
    

    public function __construct(){
       
    }
    
    
    /**
     * SQLインジェクションサニタイズ
     * @param mixed $data 文字列および配列に対応
     * @return mixed サニタイズ後のデータ
     */
    public function sqlSanitizeW(&$data){
        $this->sql_sanitize($data);
        return $data;
    }
    
    
    /**
     * SQLインジェクションサニタイズ(配列用)
     *
     * @note
     * SQLインジェクション対策のためデータをサニタイズする。
     * 高速化のため、引数は参照（ポインタ）にしている。
     *
     * @param array サニタイズデコード対象のデータ
     * @return void
     */
    public function sql_sanitize(&$data){
        
        if(is_array($data)){
            foreach($data as &$val){
                $this->sql_sanitize($val);
            }
            unset($val);
        }elseif(gettype($data)=='string'){
            $data = addslashes($data);// SQLインジェクション のサニタイズ
        }else{
            // 何もしない
        }
    }
    

}