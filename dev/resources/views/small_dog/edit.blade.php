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
	<script src="{{ asset('/js/SmallDog/edit.js')  . $ver_str}} }}" defer></script>
	
	<link href="{{ asset('/css/app.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/js/font/css/open-iconic.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/common/common.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/css/common/style.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/SmallDog/edit.css')  . $ver_str}}" rel="stylesheet">
	
	<title>子犬管理・編集フォーム</title>
	
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
	<li class="breadcrumb-item active" aria-current="page">子犬管理・編集フォーム</li>
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
		<form method="POST" action="{{ url('small_dog/update') }}" onsubmit="return checkDoublePress()">
			@csrf
			
			<input type="hidden" name="id" value="{{old('id', $ent->id)}}" />
			
			<!-- CBBXS-3007 -->
			<div class="row">
				<label for="dog_val" class="col-12 col-md-5 col-form-label">イヌ数値</label>
				<div class="col-12 col-md-7">
					<input name="dog_val" type="text"  class="form-control form-control-lg" placeholder="イヌ数値" value="{{old('dog_val', $ent->dog_val)}}">
				</div>
			</div>
			<div class="row">
				<label for="small_dog_name" class="col-12 col-md-5 col-form-label">子犬名</label>
				<div class="col-12 col-md-7">
					<input name="small_dog_name" type="text"  class="form-control form-control-lg" placeholder="子犬名" value="{{old('small_dog_name', $ent->small_dog_name)}}">
				</div>
			</div>
			<div class="row">
				<label for="small_dog_date" class="col-12 col-md-5 col-form-label">子犬日付</label>
				<div class="col-12 col-md-7">
					<input name="small_dog_date" type="text"  class="form-control form-control-lg" placeholder="子犬日付" value="{{old('small_dog_date', $ent->small_dog_date)}}">
				</div>
			</div>
			<div class="row">
				<label for="dog_type" class="col-12 col-md-5 col-form-label">犬種</label>
				<div class="col-12 col-md-7">
					<select name="dog_type" class="form-control form-control-lg">
						@foreach ($field_lccList as $dog_type => $dog_type_name)
							<option value="{{ $dog_type }}" @selected(old('dog_type', $ent->dog_type) == $dog_type)>
								{{ $dog_type_name }}
							</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<label for="dog_dt" class="col-12 col-md-5 col-form-label">子犬保護日時</label>
				<div class="col-12 col-md-7">
					<input name="dog_dt" type="text"  class="form-control form-control-lg" placeholder="子犬保護日時" value="{{old('dog_dt', $ent->dog_dt)}}">
				</div>
			</div>
			<div class="row">
				<label for="neko_flg" class="col-12 col-md-5 col-form-label">ネコフラグ</label>
				<div class="col-12 col-md-7">
					<input name="neko_flg" type="text"  class="form-control form-control-lg" placeholder="ネコフラグ" value="{{old('neko_flg', $ent->neko_flg)}}">
				</div>
			</div>
			<div class="row">
				<label for="img_fn" class="col-12 col-md-5 col-form-label">画像ファイル名</label>
				<div class="col-12 col-md-7">
					<input name="img_fn" type="text"  class="form-control form-control-lg" placeholder="画像ファイル名" value="{{old('img_fn', $ent->img_fn)}}">
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