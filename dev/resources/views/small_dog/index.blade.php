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
	<script src="{{ asset('/js/common/clm_show_hide.js')  . $ver_str}} }}" defer></script>
	<script src="{{ asset('/js/common/AutoSave.js')  . $ver_str}} }}" defer></script>
	<script src="{{ asset('/js/common/RowExchange.js')  . $ver_str}} }}" defer></script>
	<script src="{{ asset('/js/SmallDog/index.js')  . $ver_str}} }}" defer></script>
	
	<link href="{{ asset('/css/app.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/js/font/css/open-iconic.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/common/clm_show_hide.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/css/common/common.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/css/SmallDog/index.css')  . $ver_str}}" rel="stylesheet">
	
	<title>子犬管理画面</title>
	
</head>

<body>
@include('layouts.common_header')
<div class="container-fluid">

<div id="app"><!-- vue.jsの場所・未使用 --></div>



<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{ url('/') }}">ホーム</a></li>
	<li class="breadcrumb-item active" aria-current="page">子犬管理画面(見本版)</li>
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
<div id="err" class="text-danger"></div>

<!-- 検索フォーム -->
<form method="GET" action="small_dog">
		
	<input type="search" placeholder="検索" name="main_search" value="{{ old('main_search', $searches['main_search'])}}" title="子犬名、電話番号、住所、備考を部分検索します" class="form-control search_btn_x">
	<div style="display:inline-block;">
		<div id="search_dtl_div" style="display:none;">

			<input type="search" placeholder="ID" name="id" value="{{ old('id', $searches['id']) }}" class="form-control search_btn_x">
			
			<!-- CBBXS-3004 -->
			<input type="search" placeholder="イヌ数値" name="dog_val" value="{{ old('dog_val', $searches['dog_val']) }}" class="form-control search_btn_x">
			<input type="search" placeholder="子犬名" name="small_dog_name" value="{{ old('small_dog_name', $searches['small_dog_name']) }}" class="form-control search_btn_x">
			<input type="search" placeholder="子犬日付" name="small_dog_date" value="{{ old('small_dog_date', $searches['small_dog_date']) }}" class="form-control search_btn_x">
			<select name="dog_type" class="form-control search_btn_x">
				<option value=""> - 犬種 - </option>
				@foreach ($dogTypeList as $dog_type => $dog_type_name)
					<option value="{{ $dog_type }}" @selected(old('dog_type', $searches['dog_type']) == $dog_type)>
						{{ $dog_type_name }}
					</option>
				@endforeach
			</select>
			<input type="search" placeholder="子犬保護日時" name="dog_dt" value="{{ old('dog_dt', $searches['dog_dt']) }}" class="form-control search_btn_x">
			<input type="search" placeholder="ネコフラグ" name="neko_flg" value="{{ old('neko_flg', $searches['neko_flg']) }}" class="form-control search_btn_x">
			<input type="search" placeholder="画像ファイル名" name="img_fn" value="{{ old('img_fn', $searches['img_fn']) }}" class="form-control search_btn_x">
			<input type="search" placeholder="備考" name="note" value="{{ old('note', $searches['note']) }}" class="form-control search_btn_x">
			<input type="search" placeholder="順番" name="sort_no" value="{{ old('sort_no', $searches['sort_no']) }}" class="form-control search_btn_x">
			<input type="search" placeholder="IPアドレス" name="ip_addr" value="{{ old('ip_addr', $searches['ip_addr']) }}" class="form-control search_btn_x">

			<!-- CBBXE -->
			
			<select name="delete_flg" class="form-control search_btn_x">
				<option value=""> - 有効/削除 - </option>
				<option value="0" @selected(old('delete_flg', $searches['delete_flg']) == 0)>有効</option>
				<option value="1" @selected(old('delete_flg', $searches['delete_flg']) == 1)>削除</option>
			</select>
			
			<input type="search" placeholder="更新者" name="update_user" value="{{ old('update_user', $searches['update_user']) }}" class="form-control search_btn_x">
			
			<input type="number" placeholder="一覧の最大行数" name="per_page" value="{{ old('per_page', $searches['per_page']) }}" class="form-control search_btn_x" title="一覧に表示する行数">
			<button type="button" class ="btn btn-outline-secondary" onclick="$('#search_dtl_div').toggle(300);">＜ 閉じる</button>
		</div>
	</div>
	<div style="display:inline-block;">
		<button type="submit" class ="btn btn-outline-primary">検索</button>
		<button type="button" class ="btn btn-outline-secondary" onclick="$('#search_dtl_div').toggle(300);">詳細</button>
		 <a href="small_dog?clear=1" class="btn btn-outline-secondary">クリア</a>

	</div>
</form>

<div style="margin-top:0.4em;">

	<div class="tool_btn_w">
		<a href="small_dog/csv_download" class="btn btn-secondary">CSV</a>
	</div>
	
	<!-- 列表示切替機能 -->
	<div class="tool_btn_w">
		<button class="btn btn-secondary" onclick="$('#csh_div_w').toggle(300);">列表示切替</button>
		<div id="csh_div_w" style="width:100vw;" >
			<div id="csh_div" ></div><!-- 列表示切替機能の各種チェックボックスの表示場所 -->
		</div>
	</div>
	
	<div class="tool_btn_w">
		<a href="small_dog/create" class="btn btn-success">新規登録</a>
	</div>
</div>

<div id="auto_save" class="text-success"></div><!-- 自動保存のメッセージ表示区分 -->

<table id="small_dog_mng_tbl" class="table table-striped table-bordered table-condensed" style="margin-top:20px;">
	<thead>
		<tr>
			<!-- CBBXS-3035 -->
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'id', 'id') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'dog_val', 'イヌ数値') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'small_dog_name', '子犬名') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'small_dog_date', '子犬日付') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'dog_type', '犬種') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'dog_dt', '子犬保護日時') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'neko_flg', 'ネコフラグ') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'img_fn', '画像ファイル名') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'note', '備考') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'sort_no', '順番') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'delete_flg', '無効フラグ') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'update_user_id', '更新者') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'ip_addr', 'IPアドレス') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'created_at', '生成日時') !!}</th>
			<th>{!! CrudBaseHelper::sortLink($searches, 'small_dog', 'updated_at', '更新日') !!}</th>

			<!-- CBBXE -->
			<th style="width:280px"></th>
		</tr>
	</thead>
	<tbody>
		@foreach ($data as $ent)
			<tr>
				<!-- CBBXS-3005 -->
				<td>{{$ent->id}}</td>
				<td>{{$ent->dog_val}}</td>
				<td>{{$ent->small_dog_name}}</td>
				<td>{{$ent->small_dog_date}}</td>
				<td>{{ $dogTypeList[$ent->dog_type] ?? '' }}</td>
				<td>{{$ent->dog_dt}}</td>
				<td>{{$ent->neko_flg}}</td>
				<td>{{$ent->img_fn}}</td>
				<td>{{$ent->note}}</td>
				<td>{{$ent->sort_no}}</td>
				<td>{{($ent->delete_flg) ? '無効': '有効' }}</td>
				<td>{{$ent->update_user_id}}</td>
				<td>{{$ent->ip_addr}}</td>
				<td>{{$ent->created_at}}</td>
				<td>{{$ent->updated_at}}</td>

				<!-- CBBXE -->
				<td>
					
					{!! CrudBaseHelper::rowExchangeBtn($searches) !!}<!-- 行入替ボタン -->
					<a href="small_dog/show?id={{$ent->id}}" class="btn btn-info btn-sm text-light">詳細</a>
					<a href="small_dog/edit?id={{$ent->id}}" class="btn btn-primary btn-sm">編集</a>
					{!! CrudBaseHelper::disabledBtn($searches, $ent->id) !!}<!-- 削除/削除取消ボタン（無効/有効ボタン） -->
					{!! CrudBaseHelper::destroyBtn($searches, $ent->id) !!}<!-- 抹消ボタン -->
					
					
				</td>
			</tr>
		@endforeach
	</tbody>
</table>

<div>{{$data->appends(request()->query())->links('pagination::bootstrap-4')}} </div><!-- ページネーション -->

</main>
</div><!-- d-flex -->

</div><!-- container-fluid -->

@include('layouts.common_footer')

<!-- JSON埋め込み -->
<input type="hidden" id="csrf_token" value="{{ csrf_token() }}" >
{!! CrudBaseHelper::embedJson('search_json', $searches) !!}
{!! CrudBaseHelper::embedJson('data_json', $data) !!}

</body>
</html>