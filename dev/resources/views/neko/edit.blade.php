<?php 
$ver_str = '?v=' . $this_page_version;

$cbh = new CrudBaseHelper($crudBaseData);
?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<script src="{{ asset('/js/app.js') }}" defer></script>
	<script src="{{ asset('/js/common/jquery-3.6.0.min.js') }}" defer></script>
	{!! $cbh->crudBaseJs(1, $this_page_version) !!}
	<script src="{{ asset('/js/Neko/edit.js')  . $ver_str}} }}" defer></script>
	
	<link href="{{ asset('/css/app.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/js/font/css/open-iconic.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/common/common.css')  . $ver_str}}" rel="stylesheet">
	{!! $cbh->crudBaseCss(0, $this_page_version) !!}
	<link href="{{ asset('/css/Neko/edit.css')  . $ver_str}}" rel="stylesheet">
	
	<title>ネコ管理・編集フォーム</title>
	
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
	<li class="breadcrumb-item"><a href="{{ url('neko') }}">ネコ管理・一覧</a></li>
	<li class="breadcrumb-item active" aria-current="page">ネコ管理・編集フォーム</li>
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
		<form method="POST" action="{{ url('neko/update') }}" onsubmit="return checkDoublePress()">
			@csrf
			
			<input type="hidden" name="id" value="{{old('id', $ent->id)}}" />
			
			<!-- CBBXS-3007 -->
			<div class="row">
				<label for="neko_val" class="col-12 col-md-5 col-form-label">neko_val</label>
				<div class="col-12 col-md-7">
					<input name="neko_val" type="text"  class="form-control form-control-lg" placeholder="neko_val" value="{{old('neko_val', $ent->neko_val)}}">
				</div>
			</div>
			<div class="row">
				<label for="neko_name" class="col-12 col-md-5 col-form-label">neko_name</label>
				<div class="col-12 col-md-7">
					<input name="neko_name" type="text"  class="form-control form-control-lg" placeholder="neko_name" value="{{old('neko_name', $ent->neko_name)}}">
				</div>
			</div>
			<div class="row">
				<label for="neko_date" class="col-12 col-md-5 col-form-label">neko_date</label>
				<div class="col-12 col-md-7">
					<input name="neko_date" type="date"  class="form-control form-control-lg" placeholder="neko_date" value="{{old('neko_date', $ent->neko_date)}}">
				</div>
			</div>
			<div class="row">
				<label for="neko_type" class="col-12 col-md-5 col-form-label">猫種別</label>
				<div class="col-12 col-md-7">
					<select name="neko_type" class="form-control form-control-lg">
						@foreach ($nekoTypeList as $neko_type => $neko_type_name)
							<option value="{{ $neko_type }}" @selected(old('neko_type', $ent->neko_type) == $neko_type)>
								{{ $neko_type_name }}
							</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<label for="neko_dt" class="col-12 col-md-5 col-form-label">neko_dt</label>
				<div class="col-12 col-md-7">
					<input name="neko_dt" type="text"  class="form-control form-control-lg" placeholder="neko_dt" value="{{old('neko_dt', $ent->neko_dt)}}">
				</div>
			</div>
			<div class="row">
				<label for="neko_flg" class="col-12 col-md-5 col-form-label">ネコフラグ</label>
				<div class="col-12 col-md-7">
					<select name="neko_flg" class="form-control form-control-lg">
							<option value="0" @selected(old('neko_flg', $ent->neko_flg) == 0)>OFF</option>
							<option value="1" @selected(old('neko_flg', $ent->neko_flg) == 1)>ON</option>
					</select>
				</div>
			</div>
			<div class="row">
				<label for="img_fn" class="col-12 col-md-5 col-form-label">画像ファイル名</label>
				<div class="col-12 col-md-7">
					<div class='cbf_input' style="width:180px;height:auto;">
						<label for="img_fn" class="fuk_label" >
							<input type="file" id="img_fn" name="img_fn" class="img_fn" style="display:none" accept="image/*" title="画像ファイルをドラッグ＆ドロップ(複数可)" data-inp-ex='image_fuk' data-fp='' />
							<span class='fuk_msg' style="padding:20%">画像ファイルをドラッグ＆ドロップ(複数可)</span>
						</label>
					</div>
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

<!-- JSON埋め込み -->
<input type="hidden" id="csrf_token" value="{{ csrf_token() }}" >
{!! $cbh->embedJson('crud_base_json', $crudBaseData) !!}

</body>
</html>