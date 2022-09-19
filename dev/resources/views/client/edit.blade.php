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
	<script src="{{ asset('/js/Client/edit.js')  . $ver_str}} }}" defer></script>
	
	<link href="{{ asset('/css/app.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/js/font/css/open-iconic.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/common/common.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/css/common/style.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/Client/edit.css')  . $ver_str}}" rel="stylesheet">
	
	<title>顧客管理・編集フォーム</title>
	
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
	<li class="breadcrumb-item"><a href="{{ url('client') }}">顧客管理・一覧</a></li>
	<li class="breadcrumb-item active" aria-current="page">顧客管理・編集フォーム</li>
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
		<form method="POST" action="{{ url('client/update') }}" onsubmit="return checkDoublePress()">
			@csrf
			
			<input type="hidden" name="id" value="{{old('id', $ent->id)}}" />
			
			<!-- CBBXS-3007 -->
			<div class="row">
				<label for="client_name" class="col-12 col-md-5 col-form-label">顧客名</label>
				<div class="col-12 col-md-7">
					<input name="client_name" type="text"  class="form-control form-control-lg" placeholder="顧客名" value="{{old('client_name', $ent->client_name)}}">
				</div>
			</div>

			<div class="row">
				<label for="tell" class="col-12 col-md-5 col-form-label">電話番号</label>
				<div class="col-12 col-md-7">
					<input name="tell" type="text"  class="form-control form-control-lg" placeholder="電話番号" value="{{old('tell', $ent->tell)}}">
				</div>
			</div>

			<div class="row">
				<label for="address" class="col-12 col-md-5 col-form-label">住所</label>
				<div class="col-12 col-md-7">
					<input name="address" type="text"  class="form-control form-control-lg" placeholder="住所" value="{{old('address', $ent->address)}}">
				</div>
			</div>
			
			<div class="row">
				<label for="note" class="col-12 col-md-5 col-form-label">備考</label>
				<div class="col-12 col-md-7">
					<textarea name="note" id="note" class="form-control form-control-lg" placeholder="備考"  maxlength="2000">{{old('note', $ent->note)}}</textarea>
				</div>
			</div>
			<!-- CBBXE -->

			<div class="row">
				<div class="col-12" style="text-align:right">
					<button id="submit_btn" class="btn btn-warning btn-lg">変更</button>
					<div id="submit_msg" class="text-success" style="display:none" >データベースに登録中です...</div>
				</div>
			</div>
			
			
		</form>
		
	</div>
</div>

</main>
</div><!-- d-flex -->

</div><!-- container-fluid -->

@include('layouts.common_footer')
</body>
</html>