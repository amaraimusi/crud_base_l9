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
	<script src="{{ asset('/js/UserMng/edit.js')  . $ver_str}} }}" defer></script>
	
	<link href="{{ asset('/css/app.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/js/font/css/open-iconic.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/common/common.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/css/common/style.css')  . $ver_str }}" rel="stylesheet">
	<link href="{{ asset('/css/UserMng/edit.css')  . $ver_str}}" rel="stylesheet">
	
	<title>ユーザー管理・編集フォーム</title>
	
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
	<li class="breadcrumb-item"><a href="{{ url('user_mng') }}">ユーザー管理・一覧</a></li>
	<li class="breadcrumb-item active" aria-current="page">ユーザー管理・編集フォーム</li>
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
		<form method="POST" action="{{ url('user_mng/update') }}" onsubmit="return checkDoublePress()">
			@csrf

			<div class="row">
				<label for="user_mng_name" class="col-12 col-md-5 col-form-label">ID</label>
				<div class="col-12 col-md-7">{{ $ent->id }}</div>
				<input type="hidden" name="id" value="{{old('id', $ent->id)}}" />
			</div>
			
			<div class="row">
				<label for="name" class="col-12 col-md-5 col-form-label">ユーザー名</label>
				<div class="col-12 col-md-7">{{ $ent-> name}}</div>
			</div>

			<div class="row">
				<label for="email" class="col-12 col-md-5 col-form-label">メールアドレス</label>
				<div class="col-12 col-md-7">{{ $ent-> email}}</div>
			</div>

			<div class="row">
				<label for="nickname" class="col-12 col-md-5 col-form-label">名前</label>
				<div class="col-12 col-md-7">
					<input name="nickname" type="text"  class="form-control form-control-lg" placeholder="名前" value="{{old('nickname', $ent->nickname)}}">
				</div>
			</div>

			<div class="row">
				<label for="role" class="col-12 col-md-5 col-form-label">権限</label>
				<div class="col-12 col-md-7">
					<select name="role" class="form-control form-control-lg">
						@foreach ($roleList as $role => $role_name)
							<option value="{{ $role }}" @selected(old('role', $ent->role) == $role)>
								{{ $role_name }}
							</option>
						@endforeach
					</select>
				</div>
			</div>
			
			<div class="row">
				<label for="password" class="col-12 col-md-5 col-form-label">パスワード</label>
				<div class="col-12 col-md-7">
					<input name="password" type="text"  class="form-control form-control-lg" placeholder="パスワード(未入力時はパスワード変更なし)" value="{{old('password')}}">
				</div>
			</div>

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