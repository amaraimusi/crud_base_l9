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
		this.options = options;
		
		this.jqMainTbl = jQuery(options.main_tbl_slt); // メインテーブル一覧の要素オブジェクト
		
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
	
	
}













