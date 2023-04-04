<?php 
namespace App\Helpers;
use CrudBase\CrudBase;

class CrudBaseHelper
{
    
    private $crudBaseData;
    
    public function __construct(&$crudBaseData){
        $this->crudBaseData = $crudBaseData;
    }

    /**
     * 新バージョン通知区分を表示
     */
    public function divNewPageVarsion(){
        
        $new_version = $this->crudBaseData['new_version'];
        $this_page_version = $this->crudBaseData['this_page_version'];
        
        if(empty($new_version)) return;
        $html = "
			<div style='padding:10px;background-color:#fac9cc'>
				<div>新バージョン：{$this_page_version}</div>
				<div class='text-danger'>当画面は新しいバージョンに変更されています。
				セッションクリアボタンを押してください。</div>
				<input type='button' class='btn btn-danger btn-sm' value='セッションクリア' onclick='sessionClear()' >
			</div>
		";
        echo $html;
    }

    /**
     * ソート機能付きのth要素を作成する
     * @return string
     */
    public static function sortLink(&$searches, $table_name, $field, $wamei)
    {
        
        $now_sort_field = $searches['sort'] ?? ''; // 現在のソートフィールドを取得
        
        $query_param_str = ''; // クエリパラメータ文字列
        foreach ($searches as $prop => $value){
            if($prop == 'sort' || $prop == 'desc') continue;
            if($value === null) continue;
            $query_param_str .= "{$prop}={$value}&";
        }
        
        // クエリパラメータ文字列が空でないなら末尾の一文字「&」を除去
        if(!empty($query_param_str)) $query_param_str=mb_substr($query_param_str,0,mb_strlen($query_param_str)-1);

        $url = '';
        $arrow = '';
        $dire = 'asc'; // 並び向き
        if($now_sort_field == $field){
            $desc_flg = $searches['desc'] ?? 0;
            if(empty($desc_flg)){ // 並び向きが昇順である場合
                $arrow = '▲';
                $url = "?{$query_param_str}&sort={$field}&desc=1";
            }else{ // 並び向きが降順である場合
                $arrow = '▼';
                $url = "?{$query_param_str}&sort={$field}&desc=0";
            }
        }else{
            $url = "?{$query_param_str}&sort={$field}";
        }
        
        $html = "<a href='{$url}'>{$arrow}{$wamei}</a>";

        return $html;
    }
    
    /**
     * フラグを「有効」、「無効」の形式で表記する
     * @param int $flg フラグ
     * @return string
     */
    public static function tdDate($value){
        
        if(empty($value)) $value = '';
        if($value == '0000-00-00') $value = '';
        if($value == '0000-00-00 00:00') $value = '';
        if($value == '0000-00-00 00:00:00') $value = '';
        
        return $value;
    }
    
    /**
     * フラグを「有効」、「無効」の形式で表記する
     * @param int $flg フラグ
     * @return string
     */
    public static function tdFlg($flg){
        $notation = "<span class='text-success'>ON</span>";
        if(empty($flg)){
            $notation = "<span class='text-secondary'>OFF</span>";
        }
        return $notation;
    }
    
    /**
     * 無効フラグを「有効」、「無効」の形式で表記する
     * @param int $delete_flg 無効フラグ
     * @return string
     */
    public static function tdDeleteFlg($delete_flg){
        $notation = "<span class='text-success'>有効</span>";
        if(!empty($delete_flg)){
            $notation = "<span class='text-secondary'>無効</span>";
        }
        return $notation;
    }
    
    
    /**
     * 長文を折りたたみ式にする
     * @param array $ent データのエンティティ
     * @param string $field フィールド名
     * @param int $strLen 表示文字数（バイト）(省略時は無制限に文字表示）
     */
    public static function tdNote($v, $field,$str_len = null){
        
        $v2="";
        $long_over_flg = 0; // 制限文字数オーバーフラグ
        if(!empty($v)){
            $v = str_replace(array('<','>'),array('&lt;','&gt;'), $v); // XSSサニタイズ
            if($str_len === null){
                $v2 = $v;
            }else{
                if(mb_strlen($v) > $str_len){
                    $v2=mb_strimwidth($v, 0, $str_len * 2);
                    $long_over_flg = 1;
                }else{
                    $v2 = $v;
                }
            }
            $v2= str_replace('\\r\\n', ' ', $v2);
            $v2= str_replace('\\', '', $v2);
        }
        
        // ノート詳細開きボタンのHTMLを作成
        $note_detail_open_html = '';
        if($long_over_flg) {
            $note_detail_open_html = "<input type='button' class='btn btn-secondary btn-sm note_detail_open_btn' value='...' onclick=\"openNoteDetail(this, '{$field}')\" />";
        }
        
        $td = "
			<div>
				<input type='hidden' name='{$field}' value='{$v}' />
				<div class='{$field}' style='white-space:pre-wrap; word-wrap:break-word;'>{$v2}</div>
                {$note_detail_open_html}
			</div>";
        return $td;
    }
    
    
    /**
     * TD要素用の画像表示
     * @param [] $ent
     * @param string $field
     * @return string html
     */
    public static function tdImg($ent, $field){

        $fp = $ent->$field ?? null;
        
        if(empty($fp)){
            return "<img src='img/icon/none.gif' />";
        }
        
        // サニタイズ
        $fp = h($fp);
        
        $thum_fp = CrudBase::toThumnailPath($fp);

        $html = "
            <a href='{$fp}' class='js_show_modal_big_img'>
                <img src='{$thum_fp}' />
            </a>
        ";
        return $html;
    }
    
    
    /**
     * 行入替ボタンを表示する
     * @param [] $searches 検索データ
     */
    public static function rowExchangeBtn(&$searches){
        $html = '';

        // ソートフィールドが「順番」もしくは空である場合のみ、行入替ボタンを表示する。他のフィールドの並びであると「順番」に関して倫理障害が発生するため。
        if($searches['sort'] == 'sort_no' || empty($searches['sort'])){
            $html = "<input type='button' value='↑↓' onclick='rowExchangeShowForm(this)' class='row_exc_btn btn btn-info btn-sm text-light' />";
        }
       return $html;
    }
    
    
    /**
     * 削除/削除取消ボタン（無効/有効ボタン）を表示する
     * @param [] $searches 検索データ
     */
    public static function disabledBtn(&$searches, $id){
        $html = '';
        
        if(empty($searches['delete_flg'])){
            // 削除ボタンを作成
            $html = "<input type='button' data-id='{$id}' onclick='disabledBtn(this, 1)' class='btn btn-danger btn-sm text-light'  value='削除'>";
        }else{
            // 削除取消ボタンを作成
            //$html = "<buttton type='button' data-id='{$id}' onclick='disabledBtn(this, 0)' class='btn btn-success btn-sm text-light' >削除取消</button>";
            $html = "<input type='button' data-id='{$id}' onclick='disabledBtn(this, 0)' class='btn btn-success btn-sm text-light' value='削除取消'>";
        }
        return $html;
    }
    
    
    /**
     * 抹消ボタン
     * @param [] $searches 検索データ
     */
    public static function destroyBtn(&$searches, $id){
        $html = '';
        
        // 削除フラグONの時のみ、抹消ボタンを表示する
        if(!empty($searches['delete_flg'])){
            // 抹消ボタンを作成
            $html = "<input type='button' data-id='{$id}' onclick='destroyBtn(this)' class='btn btn-danger btn-sm text-light' value='抹消'>";
        }
        return $html;
    }
    

    /**
     * JSONに変換して埋め込み
     * @param [] $data
     */
    public static function embedJson($xid, $data){
        
        $jData = [];
        if(gettype($data) == 'object'){
            foreach($data as $ent){
                $jData[] = (array)$ent;
            }
            
        }elseif(gettype($data) == 'array'){
            $jData = $data;
        }else{
            throw new Exception('220709A');
        }
        
        $json = json_encode($jData, JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);
        $html = "<input type='hidden' id='{$xid}' value='{$json}'>";
        return $html;
    }
    
    
    /**
     * 金額などの数値を3桁区切り表記に変換する
     * @param int $number 任意の数値
     * @throws Exception
     * @return string 3桁区切り表記文字列
     */
    public static function amount($number){
        if($number === '' || $number === null) return null;
        if(!is_numeric($number)) throw new Exception('220711A CrudBaseHelper:amount:');
        return number_format($number);
        
        
    }
    
    
    /**
     * 複数有効/削除の区分を表示する
     * @param [] $option
     * - help_flg string ヘルプフラグ 0:ヘルプ表示しない, 1:ヘルプを表示（デフォルト）$this
     * - help_msg string ヘルプメッセージ
     */
    public function divPwms($option=[]){
        
        $help_flg = $option['help_flg'] ?? 1;
        $help_msg = $option['help_msg'] ?? "※ID列の左側にあるチェックボックスにチェックを入れてから「削除」ボタンを押すと、まとめて削除されます。<br>削除の復元は画面下側のヘルプボタンを参照してください。<br>";
        
        $help_html = '';
        if($help_flg) $help_html = "<aside>{$help_msg}</aside>";
        
        $html = "
			<div style='margin-top:10px;margin-bottom:10px'>
				<label for='pwms_all_select'>すべてチェックする <input type='checkbox' name='pwms_all_select' onclick='crudBase.pwms.switchAllSelection(this);' /></label>
				<button type='button' onclick='crudBase.pwms.action(10)' class='btn btn-success btn-sm'>有効</button>
				<button type='button' onclick='crudBase.pwms.action(11)' class='btn btn-danger btn-sm'>削除</button>
				{$help_html}
			</div>
		";
				echo $html;
    }
    
    
    /**
     * シンプルなSELECT要素を作成
     * @param string $name SELECTのname属性
     * @param string $value 初期値
     * @param array $list 選択肢
     * @param array $option オプション  要素の属性情報
     * @param array $empty 未選択状態に表示する選択肢名。nullをセットすると未選択項目は表示しない
     *
     */
    public function selectX($name,$value,$list,$option=null,$empty=null){
        
        // オプションから各種属性文字を作成する。
        $optionStr = "";
        if(!empty($option)){
            foreach($option as $attr_name => $v){
                $str = $attr_name.'="'.$v.'" ';
                $optionStr.= $str;
            }
        }
        
        
        $def_op_name = '';
        
        echo "<select  name='{$name}' {$optionStr} >";
        
        if($empty!==null){
            $selected = '';
            if($value===null){
                $selected='selected';
            }
            echo "<option value='' {$selected}>{$empty}</option>";
        }
        
        foreach($list as $v=>$n){
            $selected = '';
            if($value==$v){
                $selected='selected';
            }
            
            $n = str_replace(array('<','>'),array('&lt;','&gt;'),$n);
            
            echo "<option value='{$v}' {$selected}>{$n}</option>";
            
        }
        
        echo "</select>";
    }
    
    
    /**
     * CrudBase.jsまたは、関連スクリプト群の読み込み部分HTMLコードを作成する
     * @param string $mode モード 0:CrudBase.min.jsを読み込む   1:CrudBaseを構成するスクリプトを別個で読み込む
     * @param string $this_page_version バージョン
     * @return string HTMLコード → <script>～
     */
    public function crudBaseJs($mode, $this_page_version){

    	if($mode == 0){
    		return $this->crudBaseJsDist($this_page_version);
    	}else{
    		return $this->crudBaseJsDev($this_page_version);
    	}

    }
    
    
    /**
     * CrudBase.min.jsを読み込むHTMLコードを作成する
     * @param string $this_page_version バージョン
     * @return string HTMLコード → <script>～
     */
    public function crudBaseJsDist($this_page_version){
    	$url = url('js/CrudBase/dist/CrudBase.min.js') ;
    	$ver_str = '?v=' . $this_page_version;
    	$html = "<script src='{$url}{$ver_str}' defer></script>";
    	return $html;
    }
    
    
    /**
     * CrudBase関連スクリプト群の読み込み部分HTMLコードを作成する（スクリプト別個読込版）
     * @param string $this_page_version バージョン
     * @return string HTMLコード → <script>～
     */
    public function crudBaseJsDev($this_page_version){
    	$path = public_path('js/CrudBase/src') ;
    	$jsPaths = glob($path . '/*.js'); // ディレクトリ内のすべてのjsファイルを取得
    	
    	$jsFiles = [];
    	foreach($jsPaths as $js_path){
    		$jsFiles[] = basename($js_path);
    	}
    	
    	$jsUrls = [];
    	foreach($jsFiles as $fn){
    		$jsUrls[] = url('js/CrudBase/src/' . $fn);
    	}
    	
    	$ver_str = '?v=' . $this_page_version;
    	
    	$readScripts = [];
    	foreach($jsUrls as $js_url){
    		$readScripts[] = "<script src='{$js_url}{$ver_str}' defer></script>";
    	}
    	
    	$html = implode('', $readScripts);
    	return  $html;
    	
    }
    
    
    /**
     * CrudBase.cssまたは、関連スクリプト群の読み込み部分HTMLコードを作成する
     * @param string $mode モード 0:CrudBase.min.cssを読み込む   1:CrudBaseを構成するスクリプトを別個で読み込む
     * @param string $this_page_version バージョン
     * @return string HTMLコード → <script>～
     */
    public function crudBaseCss($mode, $this_page_version){
    	
    	if($mode == 0){
    		return $this->crudBaseCssDist($this_page_version);
    	}else{
    		return $this->crudBaseCssDev($this_page_version);
    	}
    	
    }
    
    
    /**
     * CrudBase.min.cssを読み込むHTMLコードを作成する
     * @param string $this_page_version バージョン
     * @return string HTMLコード → <script>～
     */
    public function crudBaseCssDist($this_page_version){
    	$url = url('css/CrudBase/dist/CrudBase.min.css') ;
    	$ver_str = '?v=' . $this_page_version;
    	$html = "<link href='{$url}{$ver_str}' rel='stylesheet'>";
    	return $html;
    }
    
    
    /**
     * CrudBase.css関連スクリプト群の読み込み部分HTMLコードを作成する（スクリプト別個読込版）
     * @param string $this_page_version バージョン
     * @return string HTMLコード → <script>～
     */
    public function crudBaseCssDev($this_page_version){
    	$path = public_path('css/CrudBase/src') ;
    	$jsPaths = glob($path . '/*.css'); // ディレクトリ内のすべてのjsファイルを取得
    	
    	$jsFiles = [];
    	foreach($jsPaths as $css_path){
    		$jsFiles[] = basename($css_path);
    	}
    	
    	$jsUrls = [];
    	foreach($jsFiles as $fn){
    		$jsUrls[] = url('css/CrudBase/src/' . $fn);
    	}
    	
    	$ver_str = '?v=' . $this_page_version;
    	
    	$readScripts = [];
    	foreach($jsUrls as $url){
    		$readScripts[] = "<link href='{$url}{$ver_str}' rel='stylesheet'>";
    	}
    	
    	$html = implode('', $readScripts);
    	return  $html;
    	
    }
    
    
    /**
     * 画像アップロード要素を作成する
     * @param string $xid ファイル要素のid属性
     * @param string $name ファイル要素のname属性→省略可：省略時は$xidがセットされる。
     */
    public function imgInput($xid, $name = ''){
    	
    	if(empty($name)) $name = $xid;
    	
    	$html = "
			<div class='cbf_input' style='width:100%;height:auto;'>
			
				<label for='img_fn' class='fuk_label' >
					<input type='file' id='{$xid}' name='{$name}' class='img_fn' style='display:none' accept='image/*' title='画像ファイルをドラッグ＆ドロップ(複数可)' data-inp-ex='image_fuk' data-fp='' />
					<span class='fuk_msg' style='padding:20%'>画像ファイルをドラッグ＆ドロップ(複数可)</span>
				</label>
				
			</div>
		";
    	
    	return $html;
    }
    
    
    
}








