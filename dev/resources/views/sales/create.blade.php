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
	<script src="{{ asset('/js/Sales/create.js')  . $ver_str}} }}" defer></script>
	
	<link href="{{ asset('/css/app.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/js/font/css/open-iconic.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/common/common.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/css/common/style.css')  . $ver_str }}" rel="stylesheet">
	<link href="{{ asset('/css/Sales/create.css')  . $ver_str}}" rel="stylesheet">
	
	<title>売上管理・新規登録フォーム</title>
	
</head>

<body>
@include('layouts.common_header')
<div class="container-fluid">

<div id="app"><!-- vue.jsの場所・未使用 --></div>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{ url('/') }}">ホーム</a></li>
	<li class="breadcrumb-item"><a href="{{ url('sales') }}">売上管理・一覧</a></li>
	<li class="breadcrumb-item active" aria-current="page">売上管理・新規登録フォーム</li>
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
		<form method="POST" action="{{ url('sales/store') }}" onsubmit="return checkDoublePress()">
			@csrf
			
			<div class="row">
				<label for="client_id" class="col-12 col-md-5 col-form-label">顧客</label>
				<div class="col-12 col-md-7">
					<select name="client_id" class="form-control form-control-lg">
						@foreach ($clientList as $client_id => $client_name)
							<option value="{{ $client_id }}" @selected(old('client_id') == $client_id)>
								{{ $client_name }}
							</option>
						@endforeach
					</select>
				</div>
			</div>
			
			<div class="row">
				<label for="sales_amt" class="col-12 col-md-5 col-form-label">売上額</label>
				<div class="col-12 col-md-7">
					<input name="sales_amt" type="text"  class="form-control form-control-lg" placeholder="売上額" value="{{old('sales_amt')}}">
				</div>
			</div>
			
			<div class="row">
				<label for="status" class="col-12 col-md-5 col-form-label">ステータス</label>
				<div class="col-12 col-md-7">
					<select name="status" class="form-control form-control-lg">
						@foreach ($salesStatusList as $status => $status_name)
							<option value="{{ $status }}" @selected(old('status') == $status)>
								{{ $status_name }}
							</option>
						@endforeach
					</select>
				</div>
			</div>
			
			<div class="row">
				<label for="billing_date" class="col-12 col-md-5 col-form-label">請求日</label>
				<div class="col-12 col-md-7">
					<input name="billing_date" type="date"  class="form-control form-control-lg" placeholder="請求日" value="{{old('billing_date')}}" >
				</div>
			</div>
			
			<div class="row">
				<label for="billing_amt" class="col-12 col-md-5 col-form-label">請求額</label>
				<div class="col-12 col-md-7">
					<input name="billing_amt" type="text"  class="form-control form-control-lg" placeholder="請求額" value="{{old('billing_amt')}}">
				</div>
			</div>
			
			<div class="row">
				<label for="payment_date" class="col-12 col-md-5 col-form-label">入金日</label>
				<div class="col-12 col-md-7">
					<input name="payment_date" type="date"  class="form-control form-control-lg" placeholder="入金日" value="{{old('payment_date')}}" >
				</div>
			</div>
			
			<div class="row">
				<label for="payment_amt" class="col-12 col-md-5 col-form-label">入金額</label>
				<div class="col-12 col-md-7">
					<input name="payment_amt" type="text"  class="form-control form-control-lg" placeholder="入金額" value="{{old('payment_amt')}}">
				</div>
			</div>
			
			<div class="row">
				<label for="commission" class="col-12 col-md-5 col-form-label">手数料</label>
				<div class="col-12 col-md-7">
					<input name="commission" type="text"  class="form-control form-control-lg" placeholder="手数料" value="{{old('commission')}}">
				</div>
			</div>
			
			<div class="row">
				<label for="tax" class="col-12 col-md-5 col-form-label">消費税</label>
				<div class="col-12 col-md-7">
					<input name="tax" type="text"  class="form-control form-control-lg" placeholder="消費税" value="{{old('tax', 10)}}">
				</div>
			</div>
			
			<div class="row">
				<label for="note" class="col-12 col-md-5 col-form-label">備考</label>
				<div class="col-12 col-md-7">
					<textarea name="note" id="note" class="form-control form-control-lg" placeholder="備考"  maxlength="2000">{{old('note')}}</textarea>
				</div>
			</div>

			<div class="row">
				<div class="col-12" style="text-align:right">
					<button id="submit_btn" class="btn btn-warning btn-lg">登録</button>
					<div id="submit_msg" class="text-success" style="display:none" >データベースに登録中です...</div>
				</div>
			</div>
			
			
		</form>
		
	</div>
</div>

</div><!-- container-fluid -->

@include('layouts.common_footer')
</body>
</html>