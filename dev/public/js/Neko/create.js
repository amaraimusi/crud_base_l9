
// Submitボタン2重押下対策
function checkDoublePress(){
	
	$('#submit_btn').hide(); // Submitボタンを隠す。
	$('#submit_msg').show(); // Submitメッセージを表示
	
	return true;
}