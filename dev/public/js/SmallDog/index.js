var csh; // 列表示切替機能
var rowExchange; // 行入替機能
var data; // 一覧データ
var searches; // 検索データ
var csrf_token; // CSRFトークン

var autoSave;
$(()=>{
	
	
	let data_json = $('#data_json').val();
	data = JSON.parse(data_json);
	
	let search_json = $('#search_json').val();
	searches = JSON.parse(search_json);
	
	csrf_token = $('#csrf_token').val();
	
	autoSave = new AutoSave('auto_save', csrf_token);

	chs = initClmShowHide(); // 列表示切替機能の設定と初期化
	
	
	// 行入替機能の初期化
	rowExchange = new RowExchange('small_dog_mng_tbl', data, null, (param)=>{
		// 行入替直後のコールバック
		
		// 行入替後、再び行入替しなければ3秒後に自動DB保存が実行される。
		let auto_save_url = 'small_dog/auto_save';
		autoSave.saveRequest(param.data, auto_save_url, ()=>{
			// DB保存後のコールバック
			location.reload(true); // ブラウザをリロードする
		});
	});
	
	// 新しいバージョンになった場合
	if(searches.new_version == 1){
		chs.reset(); // 列表示切替機能内のローカルストレージをクリア
	}
	
});


// 列表示切替機能の設定と初期化
function initClmShowHide(){
	
	// 一覧テーブルの列表示切替機能を設定する
	
	// 列毎に初期の列表示状態を設定する。
	// -1:列切替対象外,  0:初期時はこの列を非表示, 1:最初からこの列は表示
	let iniClmData = [
		// CBBXS-3036
		-1, // ID
		1, // イヌ数値
		1, // 子犬名
		1, // 子犬日付
		1, // 犬種
		1, // 子犬保護日時
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
	
	csh.init('small_dog_mng_tbl', 'csh_div', iniClmData);
	
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
	let url = 'small_dog/disabled'; // Ajax通信先URL
	
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
	let url = 'small_dog/destroy'; // Ajax通信先URL
	
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




