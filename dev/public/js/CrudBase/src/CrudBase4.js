/**
 * CRUD支援クラス | CrudBase.js
 * 
 * @note
 * 当クラスはSPA型のCRUDを補助的にサポートするのが目的になります。
 * バージョン3までのCrudBase.jsにはブラックボックス化している部分が多く、保守性の問題がありました。
 * 現バージョンであるバージョン4からは保守性の問題を解決するため、よりシンプル化しています。
 * 他のJavaScriptライブラリとの競合問題を考え、ベースとなるライブラリはVue.jsではなくjQueryを採用しています。
 * 
 * @license MIT
 * @since 2016-9-21 | 2023-4-17
 * @version 3.3.2
 * @histroy
 * 2024-4-17 v4.0.0 保守性の問題解決のため、大幅なリニューアルをする。
 * 2019-6-28 v2.8.3 CSVフィールドデータ補助クラス | CsvFieldDataSupport.js
 * 2018-10-21 v2.8.0 ボタンサイズ変更機能にボタン表示切替機能を追加
 * 2018-10-21 v2.6.0 フォームをアコーディオン形式にする。
 * 2018-10-2 v2.5.0 フォームドラッグとリサイズ
 * 2018-9-18 v2.4.4 フォームフィット機能を追加
 * v2.0 CrudBase.jsに名称変更、およびES6に対応（IE11は非対応）
 * v1.7 WordPressに対応
 * 2016-9-21 v1.0.0
 * 
 */
class CrudBase4{
	
	
	/**
	* コンストラクタ
	* @param {} crudBaseData 
	* @param {} options オプションパラメータ　←省略可能
	*     - string main_tbl_slt メインテーブル一覧のセレクタ
	*     - string form_slt 入力フォームのセレクタ
	*/
	constructor(crudBaseData, options){
        this.crudBaseData = crudBaseData;
		
		if (options == null) options = {};
		//if(options.main_slt == null) options.mainl_slt= 'main'; // メイン区分 ←入力フォームが表示されている時に隠す範囲のセレクタ。
		if(options.main_tbl_slt == null) options.main_tbll_slt= '#main_tbl'; // メイン一覧テーブル ←メイン一覧である<table>のセレクタ。
		if(options.form_slt == null) options.forml_slt= '#form_spa'; // 入力フォーム ← SPA型・入力フォーム。入力フォーム一つに、新規入力モード、編集モード、複製モードが含まれる。

		this.options = options;
		
		this.jqMainTbl = jQuery(options.main_tbl_slt); // メイン一覧テーブル
		this.jqForm = jQuery(options.form_slt); // 入力フォーム

		
    }
    
	
	/**
	* フィールドデータに列インデックス情報をセットします。
	* @param {} fieldData フィールドデータ
	* @return {} フィールドデータ
	*/
    setColumnIndex(fieldData){
        if(fieldData==null) fieldData = this.crudBaseData.fieldData;
        
		let clmIndexList = this.getColumnIndexs(); // 列インデックス情報を取得します。
		for(let field in clmIndexList){
			let clm_index = clmIndexList[field];
			if(fieldData[field]){
				fieldData[field]['clm_index'] = clm_index;
			}
		}
		
		return fieldData;

    }
	
	/**
	* 列インデックス情報を取得します。
	* @return {} 列インデックス情報
	*/
	getColumnIndexs(){
		let clmIndexs = {}; // 列インデックス情報
		
		let ths = this.jqMainTbl.find('thead th');
		ths.each((i, th)=>{
			let jqTh = jQuery(th);
			let field = jqTh.attr('data-field');
			if(field != null){
				clmIndexs[field] = i;
			}
		});
		
		return clmIndexs;
		
	}
	
	
	/**
	* 入力フォームのtag名やtype名などをフィールドデータにセットします。
	* @param {} fieldData フィールドデータ
	* @return {} フィールドデータ
	*/
	setFormInfoToFileData(fieldData){
		for(let field in fieldData){
			let fildEnt = fieldData[field];
			fildEnt['form_tag'] = null;
			fildEnt['form_type'] = null;
			fildEnt['form_valid_ext'] = null;
			
			let jqInp = this._getInpFromForm(field); // フォームから入力要素を取得する
			
			if(jqInp[0] == null) continue;
			
			// 入力要素のタグ名を取得し、フィールドデータにセットします。
			let form_tag = jqInp.get(0).tagName; 
			form_tag = form_tag.toLowerCase(); // 小文字化
			fildEnt['form_tag'] = form_tag;
			
			// 入力要素のtype属性を取得して、フィールドデータにセットします。
			let form_type = jqInp.attr('type'); // type属性を取得
			if(form_type != null){
				form_type = form_type.toLowerCase();
				fildEnt['form_type'] = form_type;
			}
			
			// type属性がfile系なら、一般的によく使われる拡張子群を表すコードであるoften_useを指定します。
			if(form_type=='file'){
				fildEnt['form_valid_ext'] = 'often_use';
			}
			
		}
		
		return fieldData;
	}
	

	/**
	* ファイルアップロード要素にカスタマイズを施します。
	* @param {} fieldData フィールドデータ
	* @return ｛｝ FileUploadKオブジェクトの連想配列
	*/
	customizeFileUpload(fieldData){
		this.fileUploadKList = {};
		
		for(let field in fieldData){
			let fieldEnt = fieldData[field];
			if(fieldEnt.form_type != 'file') continue;
			
			let fileUploadK = new FileUploadK();
			fileUploadK.addEvent(field, {'valid_ext':fieldEnt.form_valid_ext});

			this.fileUploadKList[field] = fileUploadK;
			
		}
		
		return this.fileUploadKList;
	}

	/**
	* FileUploadKオブジェクトの連想配列を取得する。連想配列のキーはフィールド。
	* @return {} FileUploadKオブジェクトの連想配列
	*/
	getFileUploadKList(){
		return this.fileUploadKList;
	}
	
	
	/**
	* 現在のボタンの位置から行インデックスを取得します。
	* @param object btn ボタン要素 ← jQueryオブジェクトも指定化
	* @return int row_index 行インデックス
	*/
	getRowIndexFromButtonPosition(btn){
		let jqBtn = null;
		if (btn instanceof jQuery) {
			jqBtn = btn;
		}else{
			jqBtn = jQuery(btn);
		}
		
		let tr = jqBtn.parents('tr');
		let row_index = tr.index();

		return row_index;
	}
	
	
	/**
	* メイン一覧テーブルの行インデックスに紐づく行からエンティティを取得する。→crudDataBaseの一覧データにマージしてから取得
	* @param int row_index 行インデックス
	* @return {} エンティティ
	*/
	getEntityByRowIndex(row_index){
		
		// メイン一覧テーブルの行インデックスに紐づく行からエンティティを取得する。
		let rowEnt = this._getEntityByRowIndex(row_index);
		
		// CrudBaseDataからidに紐づくエンティティを取得する
		let ent = this.getEntityFromCrudBaseData(rowEnt.id);

		// 行要素から取得したエンティティを一覧データのエンティティにマージする。
		for(let field in rowEnt){
			ent[field] = rowEnt[field];
		}
		
		// CrudBaseDataへエンティティをセットする。
		this.setEntityToCrudBaseData(ent);
		
		return ent;

	}
	
	
	/**
	* メイン一覧テーブルの行インデックスに紐づく行からエンティティを取得する
	* @param int row_index 行インデックス
	* @return {} エンティティ
	*/
	_getEntityByRowIndex(row_index){
		
		let ent = {}; // エンティティ
		
		// メイン一覧テーブルから行要素を取得する
		let tr = this.jqMainTbl.find('tr').eq(row_index + 1);
		
		let tds = tr.find('td'); // セル要素のリストを取得
		
		tds.each((clm_index,elm) => {

			// フィールドデータから列インデックスに紐づくフィールドエンティティを取得する。
			let fieldEnt = this._getFieldEntByClmIndex(clm_index);
			if(fieldEnt == null) return;
			
			let value = null;
			let td = $(elm);
			
			let origElm = td.find('.js_original_value');
			if(origElm[0]){
				let tag_name = origElm.get(0).tagName;
				tag_name = tag_name.toLowerCase();
				if(tag_name == 'input' || tag_name == 'select'){
					value = origElm.val();
				}
				else{
					value = origElm.html();
					value = value.replace(/<("[^"]*"|'[^']*'|[^'">])*>/g,''); // 文字列からタグを除去
				}
			}else{
				value = td.html();
				value = value.replace(/<("[^"]*"|'[^']*'|[^'">])*>/g,''); // 文字列からタグを除去
			}

			ent[fieldEnt.Field] = value; // エンティティへtd要素内から取得した値をセットする。

		});


		return ent;
		
		
	}
	

	/**
	* CrudBaseDataからidに紐づくエンティティを取得する
	* @param int id ID
	* @return {} エンティティ
	*/
	getEntityFromCrudBaseData(id){
		let data = this.crudBaseData.data.data;

		for(let i in data){
			let ent = data[i];
			if(ent.id == id){
				return ent;
			}
		}
		
		return null;
		
	}
	

	/**
	* CrudBaseDataへエンティティをセットする
	* @param {} pEnt エンティティ
	*/
	setEntityToCrudBaseData(pEnt){
		let data = this.crudBaseData.data.data;

		for(let i in data){
			let ent = data[i];
			if(ent.id == pEnt.id){
				data[i] = pEnt;
				break;
			}
		}
	}
		
	
	/**
	* フィールドデータから列インデックスに紐づくフィールドエンティティを取得する。
	* @param int clm_index 列インデックス
	* @return {} フィールドエンティティ
	*/
	_getFieldEntByClmIndex(clm_index){
		
		let fieldEnt = null; // フィールドエンティティ
		let fieldData = this.crudBaseData.fieldData;
		
		for(let field in fieldData){
			let fieldEnt = fieldData[field]; // フィールドエンティティ
			if(fieldEnt.clm_index == clm_index){
				return fieldEnt;
			}
		}
		
		return null;
	}
	
	
	/**
	* デフォルトエンティティを取得する
	* @return {} デフォルトエンティティ
	*/
	getDefaultEntity(){
		let ent = {};
		let fieldData = this.crudBaseData.fieldData;
		for(let field in fieldData){
			let fieldEnt = fieldData[field];
			ent[field] = fieldEnt.Default;
		}
		return ent;
	}
	
	
	/**
	* 入力フォームにエンティティを反映する
	* @param {} ent エンティティ
	*/
	setEntToForm(ent){
		
		for(let field in ent){
			let value = ent[field];
			let jqInp = this._getInpFromForm(field); // フォームから入力要素を取得する
			if(jqInp[0] != null){
				this.setValueToElement(jqInp, field, value); // 様々なタイプの要素へ値をセットする
			}
			
			// data-display属性を持つ要素のインナーへ表示する。
			let jqDisplay = this._getDisplayFromForm(field); // フォームから表示要素を取得する
			if(jqDisplay[0] != null){
				let value2 = this._xssSanitize(value);
				jqDisplay.html(value2);
			}
		}

	}
	
	
	/**
	* フォームから表示要素を取得する
	* @param string field フィールド名
	* @return object 表示要素オブジェクト
	*/
	_getDisplayFromForm(field){
		let jqDisplay = this.jqForm.find(`[data-display='${field}']`);
		return jqDisplay;
	}
	
	
	
	/**
	 * 様々なタイプの要素へ値をセットする
	 * @param inp(string or jQuery object) 要素オブジェクト、またはセレクタ
	 * @param field フィールド
	 * @param val1 要素にセットする値
	 * @param options
	 *  - form_type フォーム種別
	 *  - xss サニタイズフラグ 0:サニタイズしない , 1:xssサニタイズを施す（デフォルト）
	 *  - disFilData object[フィールド]{フィルタータイプ,オプション} 表示フィルターデータ
	 *  - dis_fil_flg 表示フィルター適用フラグ 0:OFF(デフォルト) , 1:ON
	 */
	setValueToElement(inp,field,val1,options){
		
		// 要素がjQueryオブジェクトでなければ、jQueryオブジェクトに変換。
		if(!(inp instanceof jQuery)) inp = jQuery(inp);
		
		// オプションの初期化
		if(options == null) options = {};
		if(options.xss == undefined) options.xss = 1;

		let xss = options.xss; // サニタイズフラグ

		// 入力要素のタグ名を取得する
		let tag_name = inp.get(0).tagName; 
		tag_name = tag_name.toLowerCase(); // 小文字化

		// input要素へのセット
		if(tag_name == 'input'){

			let typ = inp.attr('type'); // type属性を取得

			// チェックボックス要素へのセット
			if(typ=='checkbox'){
				if(val1 ==　0 || val1　==　null || val1　==　''){
					inp.prop("checked",false);
				}else{
					inp.prop("checked",true);
				}

			}

			// ラジオボタン要素へのセット
			else if(typ=='radio'){

				let radioParent = inp.parent();
				let opElm = radioParent.find("[value='" + val1 + "']");
				if(opElm[0]){
					opElm.prop("checked",true);
				}else{

					// ラジオボックスの選択肢に存在しない場合、すべてのチェックを外す。
					let radios = radioParent.find("[name='" + field + "']");
					radios.prop("checked",false);
	
				}

			}
			
			// file要素へのセット
			else if(typ=='file'){
				let fileUploadK = this.fileUploadKList[field];
				let midway_dp = crudBaseData.paths.public_url + '/';
				let fps = [val1];
				fileUploadK.setFilePaths(field, fps, {'midway_dp':midway_dp,});
				
			}

			// type属性がtext,hidden,date,numberなど。
			else{
				inp.val(val1);
			}

		}
		
		// SELECTへのセット
		else if(tag_name == 'select'){
			inp.val(val1);
		}

		// テキストエリア用のセット
		else if(tag_name == 'textarea'){
			inp.val(val1);
		}
		
		
	}
	
	/**
	* 入力フォーム要素内のテキストエリアの高さを自動調整する
	* @param ｛｝ fieldData フィールドデータ
	*/
	automateTextareaHeight(fieldData){
		for(let field in fieldData){
			let fieldEnt = fieldData[field];
			if(fieldEnt.form_tag == 'textarea'){
				let jqInp = this._getInpFromForm(field); // フォームから入力要素を取得する
				this._automateTextareaHeight(jqInp);　// テキストエリアの高さを自動調整する。
				
			}
		}
	}
	

	/**
	* テキストエリアの高さを自動調整する。
	* @param elm object テキストエリア要素
	*/
	_automateTextareaHeight(elm){
		let jqElm = null;
		if (elm instanceof jQuery) {
			jqElm = elm;
		}else{
			jqElm = jQuery(elm);
		}
		
		// 文字入力した時に高さ自動調整
		jqElm.attr("rows", 1).on("input", e => {
			$(e.target).height(0).innerHeight(e.target.scrollHeight);
		});
		
		// クリックしたときに自動調整
		jqElm.attr("rows", 1).click("input", e => {
			$(e.target).height(0).innerHeight(e.target.scrollHeight);
		});
	}
	
	
	/**
	* 入力フォームからエンティティを取得する
	* @param {} フィールドデータ←省略可
	* @return {} エンティティ
	*/
	getEntByForm(fieldData){
		
		if(fieldData == null) fieldData = this.crudBaseData.fieldData;
		
		let fEnt = {};
		for(let field in fieldData){

			let fieldEnt = fieldData[field];

			let jqInp = this._getInpFromForm(field); // フォームから入力要素を取得する
			
			if(jqInp[0]){
				fEnt[field] = this._getValueFromForm(jqInp, field);// 入力要素から値を取得する
			}
			
		}
		
		// CrudBaseDataの一覧データから取得したエンティティに入力フォームから取得したエンティティをマージする。
		let ent = {};
		if(fEnt.id){
			ent = this.getEntityFromCrudBaseData(fEnt.id);
			for(let field in fEnt){
				ent[field] = fEnt[field];
			}
		}else{
			ent = fEnt;
		}
		
		return ent;
	}

	
	/**
	* 入力要素から値を取得する
	* @param string field フィールド
	* @param object jqInp 入力要素オブジェクト
	* @return mixed 入力要素から取得した値
	*/
	_getValueFromForm(jqInp, field){

		let tag_name = jqInp.get(0).tagName; // 入力要素のタグ名を取得する
		tag_name = tag_name.toLowerCase(); // 小文字化
		
		// input要素へのセット
		if(tag_name == 'input'){

			let typ = jqInp.attr('type'); // type属性を取得

			// チェックボックス要素へのセット
			if(typ=='checkbox'){
				
				if(jqInp.prop('checked')){
					return 1;
				}else{
					return 0;
				}

			}

			// ラジオボタン要素へのセット
			else if(typ=='radio'){

				let opElm = jqInp.find(":checked");
				if(opElm[0]){
					return opElm.val();
				}else{
					return null;
				}

			}
			
			// file要素へのセット 
			//※注意→ ファイル名にはパスが含まれないので注意すること。バックエンド側でパスを生成するためである。
			else if(typ=='file'){
				let fileUploadK = this.fileUploadKList[field];
				let fileNameList = fileUploadK.getFileNames(field);

				if(fileNameList[0]){
					return fileNameList[0];
				}
				return null;

			}

			// type属性がtext,hidden,date,numberなど。
			else{
				return jqInp.val();
			}

		}
		
		// SELECTへのセット
		else if(tag_name == 'select'){
			return jqInp.val();
		}

		// テキストエリア用のセット
		else if(tag_name == 'textarea'){
			return jqInp.val();
		}
		
		
	}
	
	
	/**
	* バリデーション
	* @param {} fieldData フィールドデータ←省略可
	* @param {} origMsgList オリジナルメッセージリスト(省略可)→キーはフィールド
	* @return string エラーメッセージ群テキスト
	*/
	validation(fieldData, origMsgList){
		
		if(fieldData == null) fieldData = this.crudBaseData.fieldData;
		if(origMsgList == null) origMsgList = {};
		
		let err_msgs_text = '' // エラーメッセージ群テキスト
		
		for(let field in fieldData){
			
			let jqInp = this._getInpFromForm(field); // 入力要素を取得する 
			if(jqInp[0] == null) continue;
			
			let jqErr = this._getErrFromForm(field); // 入力エラー表示要素を取得する

			// バリデーションチェックをする。
			if(jqInp[0].checkValidity()){
				// 正常
				
				if(jqErr[0]) jqErr.html(''); // エラー表示要素に空文字をセット
				
			}else{
				// 入力エラーあり
				
				let err_msg;
				if(origMsgList[field]){
					err_msg = origMsgList[field];
				}else{
					err_msg = jqInp.attr('title'); // 入力要素のtitle属性からエラーメッセージを取得する
				}
				
				if(jqErr[0]) jqErr.html(err_msg); // エラー表示要素にエラーメッセージをセットする。
				err_msgs_text += `<div>${err_msg}</div>`; // エラーメッセージ群テキストにエラーメッセージを追記
				
			}
				
		}
		
		return err_msgs_text;

	}
	
	
	/**
	* 入力エラー表示要素を取得する
	* @param string field フィールド名
	* @return object 入力エラー表示要素
	*/
	_getErrFromForm(field){
		
		if(this.jqErrs == null) this.jqErrs = {};
		if(this.jqErrs[field] === undefined){
			let jqErr = this.jqForm.find(`[data-valid-err='${field}']`);
			if(jqErr[0] == null){
				this.jqErrs[field] = 0;
			}else{
				this.jqErrs[field] = jqErr;
			}
		}
		
		return this.jqErrs[field]
		 
	}
	
	
	// Check empty.
	_empty(v){
		if(v == null || v == '' || v=='0'){
			return true;
		}else{
			if(typeof v == 'object'){
				if(Object.keys(v).length == 0){
					return true;
				}
			}
			return false;
		}
	}
	
	
		/**
	 * XSSサニタイズ
	 * 
	 * @note
	 * 「<」と「>」のみサニタイズする
	 * 
	 * @param any data サニタイズ対象データ | 値および配列を指定
	 * @returns サニタイズ後のデータ
	 */
	_xssSanitize(data){
		if(typeof data == 'object'){
			for(var i in data){
				data[i] = this._xssSanitize(data[i]);
			}
			return data;
		}
		
		else if(typeof data == 'string'){
			return data.replace(/</g, '&lt;').replace(/>/g, '&gt;');
		}
		
		else{
			return data;
		}
	}
	
	
	/**
	* フォームから入力要素を取得する
	* @param string field フィールド名
	* @return object 入力要素オブジェクト
	*/
	_getInpFromForm(field){
		
		if(this.jqInps == null) this.jqInps = {};
		if(this.jqInps[field] === undefined){
			let jqInp = this.jqForm.find(`[name='${field}']`);
			if(jqInp[0] == null){
				this.jqInps[field] = 0;
			}else{
				this.jqInps[field] = jqInp;
			}
		}
		
		return this.jqInps[field]
		 
	}
	
	
	/**
	* SPA型・登録アクション
	* @param {} ent エンティティ
	* @param {} options オプション
	*    - string csrf_token CSRFトークン
	*    - function callback 登録後に実行するコールバック関数
	* @param string csrf_token CSRFトークン（省略可）
	* @return object 入力要素オブジェクト
	*/	
	regAction(ent, ajax_url, options){
		
		if(options == null) options = {};
		if(options.csrf_token == null) options.csrf_token = this._getCsrfToken();
		let csrf_token = options.csrf_token

		let fd = new FormData(); // 送信フォームデータ
		let data = {'ent':ent}; // バックエンド側に送信するデータ
		let json = JSON.stringify(data);
		fd.append( "key1", json );
		
		// CSRFトークンを送信フォームデータにセットする。
		let token = jQuery('#csrf_token').val();
		fd.append( "_token", token );
		
		fetch(ajax_url, {
			method: 'POST',
			body: fd,
		})
		.then(response => {
			return response.json();
		})
		.then(data => {
			if(options.callback){
				options.callback(); // コールバックを実行する
			}
		})
		.catch(error => {
			
			console.error(error);
			alert('通信エラー');
			
		});
	}
	
	
	/**
	* CSRFトークンを取得する
	* @return CSRFトークン
	* @throw CSRFトークン取得失敗
	*/
	_getCsrfToken(){
		let csrf_token = '';
		
		if(this.crudBaseData.csrf_token) return this.crudBaseData.csrf_token;
		
		let csrfTokenElm = $('#csrf_token');
		if(csrfTokenElm[0]){
			return csrfTokenElm.val();
		}

		throw Error('システムエラー23042515A:CSRFトークンが取得できませんでした。');
		
	}
	

	
}













