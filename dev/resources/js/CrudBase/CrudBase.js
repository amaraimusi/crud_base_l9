/**
 *  CRUD基本クラス | CrudBase.js
 * 
 * @desc
 * 管理画面を支援するクラスです。
 * 一覧の行、新規入力フォーム、編集フォームから値をエンティティの構造で取得したり、セットしたりできます。
 * 他に、管理画面でよく使われる機能を当クラスで集約して管理しています。
 * PHP側のCrudBaseパッケージとはCrudBaseDataを通して連動しています。
 * 
 * ------------------------------------------------------------------
 * 
 * 【CrudBaseの開発について】
 * 
 * CrudBaseの目的は管理画面の各種機能を支援することです。
 * あくまで支援なので当クラスが主役になるような設計は望ましくありません。
 * 一般的な管理画面でよく使われる機能を支援する形でサービスを提供します。
 * 
 * 当クラスはjQueryと依存していますが、Vue.jsや他のパッケージに依存した設計にしても保守的に問題ないです。
 * 
 * CrudBase.jsで各種モジュールを集約して管理しています。
 * 当クラスを通して各種モジュールにアクセスするようにしてください。
 * 
 * オーバーヘッドを減らしたいため、あまり使わないモジュールは初期化メソッドで実装しないようにしてください。
 * 使用頻度の低いモジュールはfactoryメソッドで対応してください。
 * 
 * 各所に散在するそれぞれのコールバックはフックで対応します。
 * このフックはWordpressプラグイン開発におけるフックと同じような概念になります。
 * 当クラスでは「hooks」として管理されます。
 * hooksに関数がセットされています。
 * 
 * @license MIT
 * @since 2016-9-21 | 2022-10-7
 * @version 4.0.0
 * @histroy
 * 2022-10-7 v4.0.0 保守性が悪い問題を解決するため大幅なリニューアルしました。使用頻度の低いモジュールを廃止し、保守性の高い設計にしています。フックの概念も導入。
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
class CrudBase{
	
	/**
	 * コンストラクタ
	 * @param {} crudBaseData PHP側のcrudBaseDataと同じデータ。様々なデータが詰め込まれています。
	 * @param {} data 一覧データ
	 * @param {} hooks フック群。 特定のイベントが実行されたタイミングで実行されるコールバック関数群が格納されます。キーでどのイベントと紐づいているか指定します。
	 *                         - after_row_rxchange 行入替後コールバック関数。行入替機能(RowExchange.js)の行入替後に実行するコールバック関数を指定します。
	 *                         - after_auto_save 自動保存後コールバック関数。自動保存機能(CrudBaseAutoSave.js)のDB更新後に実行するコールバック関数を指定します。
	 */
	constructor(crudBaseData, data, hooks){
		
		if(crudBaseData == null) crudBaseData = {};
		if(data == null) data = [];
		if(hooks == null) hooks = {};
		
		this.crudBaseData = crudBaseData;
		this.data = data;
		this.hooks = hooks;

		let h_tbl_xid = crudBaseData.h_tbl_xid; // 一覧テーブルid。  データ一覧テーブルのid属性です。
		let csrf_token = crudBaseData.csrf_token; // CSRFトークン。Ajax通信に必須のパラメータです。セキュリティために必要です。
		
		// ** 自動保存機能 **
		//     一覧に表示されているデータをすべてまとめてDB更新するのに使われます。 
		//     処理が呼び出されたから3秒後に自動保存処理が発動される仕様です。
		//     行入替後や一括削除後の時に利用されています。
		this.autoSave = new CrudBaseAutoSave(crudBaseData.auto_save_xid, csrf_token);
		
		// 行入替機能・コンポーネントの生成と初期化
		this.rowExchange = this.factoryRowExchange(
			h_tbl_xid, 
			data, 
			crudBaseData.auto_save_url,
			hooks.after_row_rxchange,
			hooks.after_auto_save);

	

		
	}
	
	
	/**
	 * 行入替機能・コンポーネントのファクトリーメソッド
	 * @param string h_tbl_xid データ一覧テーブル要素のid属性
	 * @param [] data 一覧データ
	 * @param string auto_save_url 自動保存Ajax URL。 このURLはLaravel側のアクションを指します。
	 * @param function rowExchangeCb 行入替直後コールバック。　行入替え直後に実行するコールバック関数
	 * @param function afterCallback 自動保存後コールバック関数。DB更新後（Ajax通信後）に実行するコールバック関数を指定します。
	 * @reutrn CrudBaseRowExchange 行入替機能・コンポーネント
	 */
	factoryRowExchange(h_tbl_xid, data, auto_save_url, rowExchangeCb, autoSaveCb){
		
		if(this.rowExchange) return this.rowExchange;

		// 行入替機能の初期化
		let rowExchange = new RowExchange(h_tbl_xid, data, null, (param)=>{
			
			// 行入替直後コールバックを実行する
			if(rowExchangeCb) rowExchangeCb(param);
			
			// 行入替後、再び行入替しなければ3秒後に自動DB保存が実行される。
			autoSave.saveRequest(param.data, auto_save_url, ()=>{
				
				if(autoSaveCb) autoSaveCb(param); // DB保存後のコールバック
				
				//location.reload(true); // ブラウザをリロードする■■■□□□■■■□□□
			});
		});
		
//		// 行入替機能の初期化
//		let rowExchange = new CrudBaseRowExchange(this,null,()=>{
//			this.rowExchangeAfter();
//		});
//		
//		// 行入替機能のボタン表示切替
//		let row_exc_flg =this.param.row_exc_flg;
//		this.rowExcBtnShow(row_exc_flg);

		return rowExchange;
	}
	
	
	
}