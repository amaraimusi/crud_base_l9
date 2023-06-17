<?php

namespace App\CrudBase;

//■■■□□□■■■□□□
// App::uses('Model', 'Model');
// App::uses('AppModel', 'Model');
// App::uses('IDao', 'Vendor/CrudBase');

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\CrudBase\IDao;

/**
 * Laravel用のDao 
 * @note Laravel10で動作確認済み
 * @date 2023-6-7
 * @version1.0.0
 *
 */
class DaoForLaravel extends Model implements IDao{

	public function sqlExe($sql){

		$res = \DB::select($sql); // SELECT系SQLだけでなく、INSERT系の実行も可能
		
		$data = [];
		foreach($res as $ent){
			$data[] = (array)$ent;
		}
		
		return $data;
		
	}
	
	public function begin(){
		\DB::beginTransaction();
	}
	
	public function rollback(){
		\DB::rollback();
	}
	
	public function commit(){
		\DB::commit();
	}
}