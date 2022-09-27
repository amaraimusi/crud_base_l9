<?php 
    $ver_str = "?v={$this_page_version}";
?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ダッシュボード</title>
	
	
	<script src="{{ asset('/js/app.js')   . $ver_str}}" defer></script>
	<script src="{{ asset('/js/common/jquery-3.6.0.min.js') }}" defer></script>
	<script src="{{ asset('/js/Dashboard/index.js')  . $ver_str}} }}" defer></script>
	
	<link href="{{ asset('/css/app.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/js/font/css/open-iconic.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/common/style.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/common/common.css')  . $ver_str}}" rel="stylesheet">
	

	
</head>
<body>

@include('layouts.common_header')


<div class="container-fluid">

<div id="app"><!-- vue.jsの場所・未使用 --></div>

	<div id="org_div" style="margin-top:20px;margin-bottom:60px;">
	
	
		<div class="row" style="margin-top:20px;">
		
			<!-- カード -->
			<div class="col-sm-4">
				<div class="card border-primary ">
					<div class="card-body" >
						<h5 class="card-title text-primary" style="font-weight:bold">ネコ管理（見本版）</h5>
						<div class="card_msg">
							<p class="card-text" style="height:120px">MPA型管理画面の見本です。</p>
						</div>
						<a href="{{url('neko')}}" class="btn btn-primary">ページへ移動</a>
					</div>
				</div>
			</div>
		
			<!-- カード -->
			<div class="col-sm-4">
				<div class="card border-primary ">
					<div class="card-body" >
						<h5 class="card-title text-primary" style="font-weight:bold">ネコ種別マスタ管理</h5>
						<div class="card_msg">
							<p class="card-text" style="height:120px">マスタ管理画面の見本です。</p>
						</div>
						<a href="{{url('neko_type')}}" class="btn btn-primary">ページへ移動</a>
					</div>
				</div>
			</div>
		
			<!-- カード -->
			<div class="col-sm-4">
				<div class="card border-primary ">
					<div class="card-body" >
						<h5 class="card-title text-primary" style="font-weight:bold">子犬管理</h5>
						<div class="card_msg">
							<p class="card-text" style="height:120px">子犬管理画面の見本です。</p>
						</div>
						<a href="{{url('small_dog')}}" class="btn btn-primary">ページへ移動</a>
					</div>
				</div>
			</div>

		</div><!--  row -->
	
	
		<div class="row" style="margin-top:20px;">
		
			<!-- カード -->
			<div class="col-sm-4">
				<div class="card border-primary ">
					<div class="card-body" >
						<h5 class="card-title text-primary" style="font-weight:bold">有名猫管理（見本版）</h5>
						<div class="card_msg">
							<p class="card-text" style="height:120px">SPA型管理画面の見本です。</p>
						</div>
						<a href="{{url('big_cat')}}" class="btn btn-primary">ページへ移動</a>
					</div>
				</div>
			</div>
		


		</div><!--  row -->
		
		
		<div class="row" style="margin-top:20px;">
		
			<!-- カード -->
			<div class="col-sm-4">
				<div class="card border-primary ">
					<div class="card-body" >
						<h5 class="card-title text-primary" style="font-weight:bold">売上管理（見本版）</h5>
						<div class="card_msg">
							<p class="card-text" style="height:120px">管理画面の見本です。正規の売上管理ではありません。</p>
						</div>
						<a href="{{url('sales')}}" class="btn btn-primary">ページへ移動</a>
					</div>
				</div>
			</div>
		
			<!-- カード -->
			<div class="col-sm-4">
				<div class="card border-primary ">
					<div class="card-body" >
						<h5 class="card-title text-primary" style="font-weight:bold">顧客管理（見本）</h5>
						<div class="card_msg">
							<p class="card-text" style="height:120px">管理画面の見本です。</p>
						</div>
						<a href="{{url('client')}}" class="btn btn-primary">ページへ移動</a>
					</div>
				</div>
			</div>
		
			<!-- カード -->
			<div class="col-sm-4">
				<div class="card border-primary ">
					<div class="card-body" >
						<h5 class="card-title text-primary" style="font-weight:bold">ユーザー管理</h5>
						<div class="card_msg">
							<p class="card-text" style="height:120px">ユーザー一覧の閲覧やユーザーの新規登録や編集を行えます。下位権限のユーザーのみ閲覧、登録、編集が可能です。</p>
						</div>
						<a href="{{url('user_mng')}}" class="btn btn-primary">ページへ移動</a>
					</div>
				</div>
			</div>

		</div><!--  row -->
		
	</div>

</div><!-- container-fluid -->

@include('layouts.common_footer')

</body>
</html>