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
	*/
	constructor(crudBaseData, options){
        this.crudBaseData = crudBaseData;
		
		if (options == null) options = {};
		if(options.main_tbl_slt == null) options.main_tbl_slt= '#main_tbl'; // メイン一覧テーブルのセレクタ
		if(options.form_slt == null) options.form_slt= '#form_spa'; // 入力フォームのセレクタ
		this.options = options;
		
		this.jqMainTbl = jQuery(options.main_tbl_slt); // メインテーブル一覧の要素オブジェクト
		this.jqForm = jQuery(options.form_slt); // 入力フォームの要素オブジェクト
		
    }
    
	/**
	* フィールドデータに列インデックス情報をセットします。
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
	* メイン一覧テーブルの行インデックスに紐づく行からエンティティを取得する
	* @param int row_index 行インデックス
	* @return {} エンティティ
	*/
	getEntityByRowIndex(row_index){
		
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
			if(jqInp[0] == null) continue;
			this.setValueToElement(jqInp, field, value, ent); // 様々なタイプの要素へ値をセットする
		}

	}
	
	
	/**
	* フォームから入力要素を取得する
	* @param string field フィールド名
	* @return object 入力要素オブジェクト
	*/
	_getInpFromForm(field){
		let jqInp = this.jqForm.find(`[name='${field}']`);
		return jqInp;
	}
	
	
	
	/**
	 * 様々なタイプの要素へ値をセットする
	 * @param inp(string or jQuery object) 要素オブジェクト、またはセレクタ
	 * @param field フィールド
	 * @param val1 要素にセットする値
	 * @param ent エンティティ
	 * @param options
	 *  - form_type フォーム種別
	 *  - xss サニタイズフラグ 0:サニタイズしない , 1:xssサニタイズを施す（デフォルト）
	 *  - disFilData object[フィールド]{フィルタータイプ,オプション} 表示フィルターデータ
	 *  - dis_fil_flg 表示フィルター適用フラグ 0:OFF(デフォルト) , 1:ON
	 */
	setValueToElement(inp,field,val1,ent,options){
		
		// 要素がjQueryオブジェクトでなければ、jQueryオブジェクトに変換。
		if(!(inp instanceof jQuery)) inp = jQuery(inp);
		
		// オプションの初期化
		if(options == null) options = {};
		if(options.xss == undefined) options.xss = 1;

		let xss = options.xss; // サニタイズフラグ

		// 入力要素のタグ名を取得する
		let tag_name = inp.get(0).tagName; 
		tag_name = tag_name.toLowerCase(); // 小文字化
		
		
		console.log(`tag_name＝${tag_name};field=${field};value=${val1}`);//■■■□□□■■■□□□
		
		// ▼ それぞれの入力要素の種類に従いつつ値をセットする
		if(tag_name == 'input'){

			var typ = inp.attr('type'); // type属性を取得

			if(typ=='checkbox'){
				if(val1 ==　0 || val1　==　null || val1　==　''){
					console.log('A1');//■■■□□□■■■□□□
					inp.prop("checked",false);
				}else{
					console.log('A2');//■■■□□□■■■□□□
					inp.prop("checked",true);
				}

			}

			else if(typ=='radio'){
				var opElm = options.par.find("[name='" + field + "'][value='" + val1 + "']");
				if(opElm[0]){
					opElm.prop("checked",true);
				}else{
					// 値が空である場合、ラジオボタンのすべての要素からチェックをはずす。
					if(this._empty(val1)){
						let radioParent = inp.parent();
						let radios = radioParent.find("[name='" + field + "']");
						radios.prop("checked",false);
					}
				}

			}

			// type属性がtext,hidden,date,numberなど。
			else{
				inp.val(val1);

			}

		}
		
		else if(tag_name == 'select'){
			inp.val(val1);
		}

		// テキストエリア用のセット
		else if(tag_name == 'textarea'){
/*■■■□□□■■■□□□
			if(val1!="" && val1!=null){
				val1=val1.replace(/<br>/g,"\r");
				val1 = this._xssSanitaizeDecode(val1);
			}*/
			inp.val(val1);
		}
		
		//var typ = inp.attr('type');
		
		
/*
		// 拡張型の入力要素への反映
		var inp_ex = inp.attr('data-inp-ex');

		if(inp_ex){
			switch(inp_ex){
			case 'image1':
				this._setEntToImage1(inp, field, val1); // 画像1型
				break;
			case 'image_fuk':
				this._setEntToImageFuk(inp, field, val1); // 画像FUK型
				break;
			}
			return;
		}*/
/*
		// 値を入力フォームにセットする。
		if(tag_name == 'INPUT' || tag_name == 'SELECT'){

			// type属性を取得
			var typ = inp.attr('type');

			if(typ=='checkbox'){
				if(val1 ==0 || val1==null || val1==''){
					inp.prop("checked",false);
				}else{
					inp.prop("checked",true);
				}

			}

			else if(typ=='radio'){
				var opElm = options.par.find("[name='" + field + "'][value='" + val1 + "']");
				if(opElm[0]){
					opElm.prop("checked",true);
				}else{
					// 値が空である場合、ラジオボタンのすべての要素からチェックをはずす。
					if(this._empty(val1)){
						var radios = options.par.find("[name='" + field + "']");
						radios.prop("checked",false);
					}
				}

			}

			else{

				// カスタム型のセット
				this._setForCustumType(inp,field,val1,ent,options);

			}

		}

		// テキストエリア用のセット
		else if(tag_name == 'TEXTAREA'){

			if(val1!="" && val1!=null){
				val1=val1.replace(/<br>/g,"\r");
				val1 = this._xssSanitaizeDecode(val1);
			}
			inp.val(val1);
		}

		
		else{
			if( typeof val1 == 'string'){
				val1=val1.replace(/<br>/g,"\r");
				// XSSサニタイズを施す
				if(xss == 1){
					val1 = this._xssSanitaizeEncode(val1); 
				}
				val1 = this._nl2brEx(val1);// 改行コートをBRタグに変換する
			}
			inp.html(val1);
		}*/
		
		
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
	
	
}













