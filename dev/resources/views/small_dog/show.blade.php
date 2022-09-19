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
	<link href="{{ asset('/css/common/style.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/Sales/show.css')  . $ver_str}}" rel="stylesheet">
	
	<title>子犬管理・詳細表示</title>
	
</head>

<body>
@include('layouts.common_header')
<div class="container-fluid">

<div id="app"><!-- vue.jsの場所・未使用 --></div>

<div class="d-flex flex-row m-1 m-sm-4 px-sm-5 px-1">
<main class="flex-fill mx-sm-2 px-sm-5 mx-1 px-1 w-100">

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{ url('/') }}">ホーム</a></li>
	<li class="breadcrumb-item"><a href="{{ url('small_dog') }}">子犬管理・一覧</a></li>
	<li class="breadcrumb-item active" aria-current="page">子犬管理・詳細フォーム</li>
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
		<form method="POST" action="{{ url('small_dog/update') }}">
			@csrf
			
			<input type="hidden" name="id" value="{{old('id', $ent->id)}}" />
			
			<!-- CBBXS-3008 -->
			<div class="row">
				<label for="id" class="col-12 col-md-5 col-form-label">id</label>
				<div class="col-12 col-md-7">{{ $ent-> id}}</div>
			</div>
			<div class="row">
				<label for="dog_val" class="col-12 col-md-5 col-form-label">イヌ数値</label>
				<div class="col-12 col-md-7">{{ $ent-> dog_val}}</div>
			</div>
			<div class="row">
				<label for="small_dog_name" class="col-12 col-md-5 col-form-label">子犬名</label>
				<div class="col-12 col-md-7">{{ $ent-> small_dog_name}}</div>
			</div>
			<div class="row">
				<label for="small_dog_date" class="col-12 col-md-5 col-form-label">子犬日付</label>
				<div class="col-12 col-md-7">{{ $ent-> small_dog_date}}</div>
			</div>
			<div class="row">
				<label for="dog_type" class="col-12 col-md-5 col-form-label">犬種</label>
				<div class="col-12 col-md-7">{{ $dogTypeList[$ent->dog_type] ?? '' }}</div>
			</div>
			<div class="row">
				<label for="dog_dt" class="col-12 col-md-5 col-form-label">子犬保護日時</label>
				<div class="col-12 col-md-7">{{ $ent-> dog_dt}}</div>
			</div>
			<div class="row">
				<label for="neko_flg" class="col-12 col-md-5 col-form-label">ネコフラグ</label>
				<div class="col-12 col-md-7">{{ $ent-> neko_flg}}</div>
			</div>
			<div class="row">
				<label for="img_fn" class="col-12 col-md-5 col-form-label">画像ファイル名</label>
				<div class="col-12 col-md-7">{{ $ent-> img_fn}}</div>
			</div>
			<div class="row">
				<label for="note" class="col-12 col-md-5 col-form-label">備考</label>
				<div class="col-12 col-md-7">
					<div style="white-space:pre-wrap; word-wrap:break-word;">{{ $ent->note }}</div>
				</div>
			</div>

			<!-- CBBXE -->

			<div class="row">
				<div class="col-12" style="text-align:center">
					<a href="edit?id={{$ent->id}}" class="btn btn-primary">編集</a>
				</div>
			</div>
			
			
		</form>
		
	</div>
</div>

</main>
</div><!-- d-flex -->

</div><!-- container-fluid -->

@include('layouts.common_footer')

<!-- JSON埋め込み -->
<input type="hidden" id="csrf_token" value="{{ csrf_token() }}" >

</body>
</html>