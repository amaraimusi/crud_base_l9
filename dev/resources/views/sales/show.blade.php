<?php 
$ver_str = '?v=' . $this_page_version;
?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<script src="{{ asset('/js/app.js') }}" defer></script>
	<script src="{{ asset('/js/common/jquery-3.6.0.min.js') }}" defer></script>
	<script src="{{ asset('/js/Sales/show.js')  . $ver_str}} }}" defer></script>
	
	<link href="{{ asset('/css/app.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/js/font/css/open-iconic.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/common/common.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/css/common/style.css')  . $ver_str }}" rel="stylesheet">
	<link href="{{ asset('/css/Sales/show.css')  . $ver_str}}" rel="stylesheet">
	
	<title>売上管理・詳細表示</title>
	
</head>

<body>
@include('layouts.common_header')
<div class="container-fluid">

<div id="app"><!-- vue.jsの場所・未使用 --></div>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{ url('/') }}">ホーム</a></li>
	<li class="breadcrumb-item"><a href="{{ url('sales') }}">売上管理・一覧</a></li>
	<li class="breadcrumb-item active" aria-current="page">売上管理・詳細フォーム</li>
  </ol>
</nav>

<!-- バリデーションエラーの表示 -->
@if ($errors->any())
	<div class="alert alert-danger">
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif

<div>
	<div class="form_w" >
		<form method="POST" action="{{ url('sales/update') }}">
			@csrf
			
			<input type="hidden" name="id" value="{{old('id', $ent->id)}}" />
			
			<div class="row">
				<label for="client_id" class="col-12 col-md-5 col-form-label">顧客</label>
				<div class="col-12 col-md-7">{{ $clientList[$ent->client_id] ?? '' }}</div>
			</div>
			
			<div class="row">
				<label for="sales_amt" class="col-12 col-md-5 col-form-label">売上額</label>
				<div class="col-12 col-md-7">&yen;{{ CrudBaseHelper::amount($ent->sales_amt) ?? ' - ' }}</div>
			</div>
			
			<div class="row">
				<label for="status" class="col-12 col-md-5 col-form-label">ステータス</label>
				<div class="col-12 col-md-7">{{ $salesStatusList[$ent->status] ?? '' }}</div>
			</div>
			
			<div class="row">
				<label for="billing_date" class="col-12 col-md-5 col-form-label">請求日</label>
				<div class="col-12 col-md-7">{{ $ent-> billing_date}}</div>
			</div>
			
			<div class="row">
				<label for="billing_amt" class="col-12 col-md-5 col-form-label">請求額</label>
				<div class="col-12 col-md-7">&yen;{{ CrudBaseHelper::amount($ent->billing_amt) ?? ' - ' }}</div>
			</div>
			
			<div class="row">
				<label for="payment_date" class="col-12 col-md-5 col-form-label">入金日</label>
				<div class="col-12 col-md-7">{{ $ent-> payment_date}}</div>
			</div>
			
			<div class="row">
				<label for="payment_amt" class="col-12 col-md-5 col-form-label">入金額</label>
					<div class="col-12 col-md-7">&yen;{{ CrudBaseHelper::amount($ent->payment_amt) ?? ' - '  }}</div>
			</div>
			
			<div class="row">
				<label for="commission" class="col-12 col-md-5 col-form-label">手数料</label>
				<div class="col-12 col-md-7">&yen;{{ CrudBaseHelper::amount($ent->commission) ?? ' - '  }}</div>
			</div>
			
			<div class="row">
				<label for="tax" class="col-12 col-md-5 col-form-label">消費税</label>
				<div class="col-12 col-md-7">{{ $ent->tax ?? ' - '}}%</div>
			</div>
			
			<div class="row">
				<label for="note" class="col-12 col-md-5 col-form-label">備考</label>
				<div class="col-12 col-md-7">
					<div style="white-space:pre-wrap; word-wrap:break-word;">{{ $ent->note }}</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12" style="text-align:center">
					<a href="edit?id={{$ent->id}}" class="btn btn-primary">編集</a>
				</div>
			</div>
			
			
		</form>
		
	</div>
</div>

</div><!-- container-fluid -->

@include('layouts.common_footer')

<!-- JSON埋め込み -->
<input type="hidden" id="csrf_token" value="{{ csrf_token() }}" >

</body>
</html>