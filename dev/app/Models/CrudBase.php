<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    
    
    /**
     * フィールドデータへＤＢからのフィールド詳細情報を追加
     * @param [] $fieldData フィールドデータ
     * @param string $tbl_name DBテーブル名
     * @return [] フィールド詳細情報を追加したフィールドデータ
     */
    protected function addFieldDetailsFromDB(&$fieldData, $tbl_name){
    	
    	$fieldDataDb = $this->getFieldDataFromDb($tbl_name); // DBテーブルから各フィールドの詳細情報を取得します。
    	
    	foreach($fieldDataDb as $entD){
    		$field = $entD->Field;
    		if (empty($fieldData[$field])) $fieldData[$field] = [];
    		$fEnt = $fieldData[$field];
    		
    		$fEnt['Field'] = $entD->Field;
    		$fEnt['Type'] = $entD->Type;
    		$fEnt['Collation'] = $entD->Collation;
    		$fEnt['Null'] = $entD->Null;
    		$fEnt['Key'] = $entD->Key;
    		$fEnt['Default'] = $entD->Default;
    		$fEnt['Extra'] = $entD->Extra;
    		$fEnt['Privileges'] = $entD->Privileges;
    		$fEnt['Comment'] = $entD->Comment;
    		
    		$fieldData[$field] = $fEnt;
    	}
    	
    	// 型長とデータ型を取得する
    	foreach($fieldData as &$fEnt){
    		if(empty($fEnt['Type'])) continue;
    		$data_type = $fEnt['Type'];
    		
    		// 型長を取得する
    		$matches = null;
    		preg_match('/\d+/', $data_type, $matches);
    		$fEnt['long'] = $matches[0] ?? null;
    		
    		// データ型を取得する
    		$fEnt['type'] = preg_replace('/\([^)]+\)/', '', $data_type); // カッコとカッコ内の文字列を削除した文字列を取得する
    		
    	}
    	unset($fEnt);
    	
    	return $fieldData;
    }
    
    
    
    /**
     * DBテーブルから各フィールドの詳細情報を取得します。
     * @param string $tbl_name DBテーブル名
     * @return [] 各フィールドの詳細情報
     */
    protected function getFieldDataFromDb($tbl_name){
    	$sql="SHOW FULL COLUMNS FROM {$tbl_name}";
    	$res = DB::select($sql);
    	
    	return $res;
    }
    
    
    /**
     * フィールドデータに登録対象フラグを追加します。
     * @param [] $fieldData フィールドデータ
     * @param [] $fillable 登録対象フィルタデータ
     */
    protected function addRegFlgToFieldData(&$fieldData, $fillable){
    	
    	foreach($fillable as $fill_field){
    		if(empty($fieldData[$fill_field])){
    			$fieldData[$fill_field]['reg_flg'] = 0;
    		}else{
    			$fieldData[$fill_field]['reg_flg'] = 1;
    		}
    	}
    	
    	return $fieldData;
    	
    }
    

}