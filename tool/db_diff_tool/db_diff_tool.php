<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="google" content="notranslate" />
   	<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>DBテーブル情報閲覧ツール | ワクガンス</title>
	<link rel='shortcut icon' href='/home/images/favicon.ico' />
	
	<link href="/note_prg/css/bootstrap.min.css" rel="stylesheet">
	<link href="/note_prg/css/common2.css" rel="stylesheet">

	<script src="/note_prg/js/jquery3.js"></script>	<!-- jquery-3.3.1.min.js -->
	<script src="/note_prg/js/bootstrap.min.js"></script>
	<script src="/note_prg/js/livipage.js"></script>
	<script src="/note_prg/js/ImgCompactK.js"></script>

	<style>
	   .danger{
	       color:red;
	        font-weight:bold;
	   }
	   .field_tbl_div{
	       margin-top:40px;
	   }
	</style>
</head>
<body>
<div id="header" ><h1>DBテーブル比較ツール | ワクガンス</h1></div>

<p>バージョン 0.0.1</p>

<?php 

$server_name=$_SERVER['SERVER_NAME'];
if($server_name != 'localhost') die('ローカル環境 only');
	
$home_dp = dirname(dirname(__DIR__));
require_once 'Model.php';
require_once 'PdoDao.php';

$model = new Model();


// DB群データ
$dbs = [
        'sakerui_hanbai' => [
            'host' => 'localhost',
            'db_name' => 'sakerui_hanbai',
            'user' => 'root',
            'pw' => '',
        ],
        'salesmanagement' => [
            'host' => 'localhost',
            'db_name' => 'salesmanagement',
            'user' => 'root',
            'pw' => '',
            ],
        'wakgance_sakerui_hanbai' => [
            'host' => 'localhost',
            'db_name' => 'wakgance_sakerui_hanbai',
            'user' => 'root',
            'pw' => '',
        ],
    
];

$allTblNames = [];

foreach($dbs as $i => $dbEnt){
    $dao = new PdoDao($dbEnt);
    
    $model->setDao($dao);
    
    $tblNames = $model->getTbls();
    $dbEnt['tblNames'] = $tblNames;
    
    $data = [];
    foreach($tblNames as $tbl_name){
        $fields = $model->getFields($tbl_name);
        $data[$tbl_name] = $fields;
    }
    
    $dbEnt['data'] = $data;
    $dbs[$i] = $dbEnt;
    
    $allTblNames = array_merge($allTblNames, $tblNames);
    
}

// 統合テーブル名データを取得
$allTblNames = array_unique($allTblNames);

// 統合フィールドデータを取得する
$allFields = $model->createAllFields($allTblNames, $dbs);

$model->outputTblDiffHTbls($dbs, $allTblNames);// テーブル名比較表の作成

$model->outputFieldDiffHTbl($dbs, $allTblNames, $allFields); // フィールド比較表の作成

function dump($var){
    echo '<pre>';
    var_dump($var);//■■■□□□■■■□□□)
    echo '</pre>';
}

?>
<div class="yohaku"></div>
<ol class="breadcrumb">
	<li><a href="index.html">開発ツールホーム</a></li>
	<li>DBテーブル情報閲覧ツール</li>
</ol>

<div id="footer">(C) kenji uehara 2019-10-26</div>
</body>
</html>