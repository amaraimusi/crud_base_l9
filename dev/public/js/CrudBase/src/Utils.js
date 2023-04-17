/**
 * 汎用関数集
 * @since 2023-4-13
 * @version 1.0.0
 */


/**
 * onsubmitイベントのバリデーション
 * @param string form_xid フォームID
 * @return string エラーメッセージ
 */
function g_onsubmitValidation(form_xid){
    let inputs = $('#' + form_xid + ' input,select');
    let err_msg = '';
    
    inputs.each((i,elm) => {
        let valid_res = elm.checkValidity();
        
        if(valid_res == false){
            let title = $(elm).attr('title');
            err_msg += `<div>${title}</div>`;
        }
        
    });

    if(err_msg != '') return err_msg;
    
    return false;
}


// テキストエリアの高さを自動調整する。
function g_automateTextareaHeight(slt){

        let taElm = $(slt);
        
        // 文字入力した時に高さ自動調整
        taElm.attr("rows", 1).on("input", e => {
            $(e.target).height(0).innerHeight(e.target.scrollHeight);
        });
        
        // クリックしたときに自動調整
        taElm.attr("rows", 1).click("input", e => {
            $(e.target).height(0).innerHeight(e.target.scrollHeight);
        });
}

/**
 * メイン一覧テーブルの列詳細情報を取得する
 * @param string tbl_xid メイン一覧テーブルのid属性
 * @return [] 列詳細情報
 */
function g_getColumnInfo(tbl_xid){
	
	let clmInfo = [];
    let jqThList = $(`#${tbl_xid} thead th`);
	
	jqThList.each((i,elm) => {
		let jqElm = $(elm);
		let th_html = jqElm.html();
		
		let field = '';
		let tbl_name='';
		let infoElm = jqElm.find("[name='field_info']");
		if(infoElm[0]){
			field = infoElm.val();
			tbl_name = infoElm.attr('data-table');
		}
		
		let ent = {
			'th_html':th_html,
			'field':field,
			'tbl_name':tbl_name,
			'index':i,
		};
		
		clmInfo.push(ent);
	});
	
	return clmInfo;
	
	
}
    