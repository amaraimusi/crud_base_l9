let crudBase; // CRUD支援オブジェクト
let csh; // 列表示切替機能
let rowExchange; // 行入替機能
let crudBaseData;
let data; // 一覧データ
let searches; // 検索データ
let csrf_token; // CSRFトークン
let baseXHelper; // 基本X
//let formModalCat; // 入力フォームのモーダル制御オブジェクト//■■■□□□■■■□□□
let jqMain; // メインコンテンツ
let jqMainTbl; // 一覧テーブルのjQueryオブジェクト
let jqForm; // SPA型入力フォームのjQueryオブジェクト
let jqValidErrMsg; // バリデーションエラーメッセージ表示要素
let jqRegistMsg; // 登録成功メッセージ要素	←「登録中」、「登録しました」などのメッセージを表示する。
let jqCreateMode; // 新規入力モード ←新規入力モードのみ表示するセレクタ
let jqEditMode; // 編集モード ←編集モードのみ表示するセレクタ

var autoSave;
$(()=>{
    
	
	baseXHelper = new BaseXHelper();
    
    let crud_base_json = $('#crud_base_json').val();
    crudBaseData = JSON.parse(crud_base_json);
    data = crudBaseData.data;
    searches = crudBaseData.searches;
    
	// CRUD支援オブジェクト
	crudBase = new CrudBase4(crudBaseData,{
		'main_tbl_slt': '#main_tbl', // メイン一覧テーブル ←メイン一覧である<table>のセレクタ。
		'form_slt': '#form_spa', // 入力フォーム ← SPA型・入力フォーム。入力フォーム一つに、新規入力モード、編集モード、複製モードが含まれる。
	});
	
	// 列の順番である列インデックスをフィールドデータにセットします。
	crudBaseData.fieldData = crudBase.setColumnIndex(crudBaseData.fieldData); 
	
	// 入力フォームのtag名やtype名などをフィールドデータにセットします。
	crudBaseData.fieldData = crudBase.setFormInfoToFileData(crudBaseData.fieldData); 
	
	// ファイルアップロード要素にカスタマイズを施します。← カスタマイズにより、画像プレビューやファイル情報を表示などができるようになります。
	crudBase.customizeFileUpload(crudBaseData.fieldData);
	
	// 入力フォーム要素内のテキストエリアの高さを自動調整する
	crudBase.automateTextareaHeight(crudBaseData.fieldData);

	csrf_token = $('#csrf_token').val();
	
	autoSave = new AutoSave('auto_save', csrf_token);

	chs = initClmShowHide(); // 列表示切替機能の設定と初期化
	
	
	// 行入替機能の初期化
	rowExchange = new RowExchange('main_tbl', data, null, (param)=>{
		// 行入替直後のコールバック
		
		// 行入替後、再び行入替しなければ3秒後に自動DB保存が実行される。
		let auto_save_url = 'neko/auto_save';
		autoSave.saveRequest(param.data, auto_save_url, ()=>{
			// DB保存後のコールバック
			location.reload(true); // ブラウザをリロードする
		});
	});
	
	// 新しいバージョンになった場合
	if(searches.new_version == 1){
		chs.reset(); // 列表示切替機能内のローカルストレージをクリア
	}
	
    // 一覧中のサムネイル画像をクリックしたら画像をモーダル化しつつ大きく表示する。
    let showModalBigImg = new ShowModalBigImg('.js_show_modal_big_img');
    
    jqMain =  $('main'); // メインコンテンツ
	jqMainTbl = $('#main_tbl'); // 一覧テーブル
	jqForm = $('#form_spa'); // SPA型・入力フォーム
	jqValidErrMsg = $('.js_valid_err_msg'); // バリデーションエラーメッセージ表示要素
	jqRegistMsg = $('.js_registering_msg'); // 登録成功メッセージ要素	←「登録中」、「登録しました」などのメッセージを表示する。
	jqCreateMode = $('.js_create_mode'); // 新規入力モードのみ表示する要素
	jqEditMode = $('.js_edit_mode'); // 編集モードのみ表示する要素
	//■■■□□□■■■□□□
	//let clmInfo = g_getColumnInfo('main_tbl');

    
});



// 列表示切替機能の設定と初期化
function initClmShowHide(){

	// 一覧テーブルの列表示切替機能を設定する
	
	// 列毎に初期の列表示状態を設定する。
	// -1:列切替対象外,  0:初期時はこの列を非表示, 1:最初からこの列は表示
	let iniClmData = [
		// CBBXS-3036
		-1, // ID
		1, // neko_val
		1, // neko_name
		1, // neko_date
		1, // 猫種別
		1, // neko_dt
		1, // ネコフラグ
		1, // 画像ファイル名
		1, // 備考
		0, // 順番
		0, // 無効フラグ
		0, // 更新者
		0, // IPアドレス
		0, // 生成日時
		0, // 更新日

		// CBBXE
		-1 // ボタン列
	];
	
	let csh = new ClmShowHide();
	
	csh.init('main_tbl', 'csh_div', iniClmData);
	
	return csh;
}


/**
 * 行入替機能のフォームを表示
 * @param btnElm ボタン要素
 */
function rowExchangeShowForm(btnElm){
	rowExchange.showForm(btnElm); // 行入替フォームを表示する
}

/**
 * 削除/削除取消ボタンのクリック
 * @param object btnElm 削除、または削除取消ボタン要素
 * @param int action_flg 0:削除取消, 1:削除
 */
function disabledBtn(btnElm, action_flg){

	if(action_flg == 1 && !window.confirm("削除してもよろしいですか")){
		return;
	}

	let jqBtn = $(btnElm);
	let id = jqBtn.attr('data-id');
	
	let data = {
		'id':id,
		'action_flg':action_flg,
		
	}
	
	let json_str = JSON.stringify(data);//データをJSON文字列にする。
	let url = 'neko/disabled'; // Ajax通信先URL
	
	let fd = new FormData(); // 送信フォームデータ
	fd.append( "key1", json_str );
	
	// CSRFトークンを送信フォームデータにセットする。
	fd.append( "_token", csrf_token );
	
	// AJAX
	jQuery.ajax({
		type: "post",
		url: url,
		data: fd,
		cache: false,
		dataType: "text",
		processData: false,
		contentType: false,
	})
	.done((str_json, type) => {
		let res;
		try{
			res =jQuery.parseJSON(str_json);

		}catch(e){
			jQuery("#err").html(str_json);
			return;
		}
		
		location.reload(true); // ブラウザをリロード
		
	})
	.fail((jqXHR, statusText, errorThrown) => {
		console.log(jqXHR);
		jQuery('#err').html(jqXHR.responseText);
	});
}


/**
 * 抹消ボタンのクリック
 * @param object btnElm 抹消ボタン要素
 */
function destroyBtn(btnElm){
	
	if(!window.confirm("元に戻せませんが抹消してもよろしいですか？")){
		return;
	}
	
	let jqBtn = $(btnElm);
	let id = jqBtn.attr('data-id');
	
	let data = {
		'id':id,
	}
	
	let json_str = JSON.stringify(data);//データをJSON文字列にする。
	let url = 'neko/destroy'; // Ajax通信先URL
	
	let fd = new FormData(); // 送信フォームデータ
	fd.append( "key1", json_str );
	
	// CSRFトークンを送信フォームデータにセットする。
	fd.append( "_token", csrf_token );
	
	// AJAX
	jQuery.ajax({
		type: "post",
		url: url,
		data: fd,
		cache: false,
		dataType: "text",
		processData: false,
		contentType: false,
	})
	.done((str_json, type) => {
		let res;
		try{
			res =jQuery.parseJSON(str_json);

		}catch(e){
			jQuery("#err").html(str_json);
			return;
		}
		
		location.reload(true); // ブラウザをリロード
		
	})
	.fail((jqXHR, statusText, errorThrown) => {
		console.log(jqXHR);
		jQuery('#err').html(jqXHR.responseText);
	});
}


/**
 * ノート詳細を開く
 * @param btnElm 詳細ボタン要素
 */
function openNoteDetail(btnElm,field){
	return baseXHelper.openNoteDetail(btnElm,field);
}









/////////// 以下はSPA型・入力フォーム関連

/**
 * SPA型・新規入力ボタン押下時の処理
 */
function clickCreateBtn(btn){
	
	// SPA型・入力フォーム画面を開く
	_showForm();
}

/**
 * SPA型・編集ボタン押下時の処理
 */
function clickEditBtn(btn){
	
    // 現在のボタンの位置から行インデックスを取得します。
    let row_index = crudBase.getRowIndexFromButtonPosition(btn);
	
	// SPA型・入力フォーム画面を開く
	_showForm(row_index);
}

/**
 * SPA型・入力フォーム画面を開く
 * @note フォーム画面はSPA型であり、新規入力と編集に対応する
 * @param int row_index 行インデックス ← メイン一覧テーブルの行番　← 未セットなら新規入力、セットすれば編集という扱いになる。
 */
function _showForm(row_index){
	
	let inp_mode = 'create'; // 新規入力モード
	if(row_index != null) inp_mode = 'edit'; // 編集モード
	
	let ent = {};
	if(inp_mode == 'create'){
		// デフォルトエンティティを取得する
		ent = crudBase.getDefaultEntity();
	}else{
		// メイン一覧テーブルの行インデックスに紐づく行からエンティティを取得する
		ent = crudBase.getEntityByRowIndex(row_index);
	}
	
	crudBase.setEntToForm(ent); // 入力フォームにエンティティを反映する
	
	// 新規入力モード、編集モードのそれぞれの表示切替
	if(inp_mode=='create'){
		jqCreateMode.show();
		jqEditMode.hide();
	}else{
		jqCreateMode.hide();
		jqEditMode.show();
	}
	
	jqValidErrMsg .html(''); // エラーメッセージをクリア
	jqRegistMsg.html(''); // 登録中のメッセージをクリア

	jqMain.hide(); // メイン一覧テーブルを隠す
	jqForm.show(); // 入力フォームを表示する
	
}

/**
 * SPA型・入力フォーム画面を閉じる
 */
function closeForm(){
	jqMain.show();
	jqForm.hide();
}

/**
 * SPA型・入力フォーム画面の登録ボタン、または変更ボタン押下アクション
 * @note 新規入力、編集、複製に関わらず当メソッドを呼び出す。
 */
function regAction(){
	
	// バリデーションによる入力チェック
	let err_msg = crudBase.validation(null);
	jqValidErrMsg.html(err_msg);
	
	// 入力フォームからエンティティを取得する
	let ent = crudBase.getEntByForm();
	
	jqRegistMsg.html('登録中です...');
	
	// SPA型・登録アクション
	crudBase.regAction(ent,'neko/reg_action', {
		'callback': (param)=>{
			// DB登録後のコールバック処理内容
			jqRegistMsg.html('登録しました。'); 
		}
	});
	
	
}




