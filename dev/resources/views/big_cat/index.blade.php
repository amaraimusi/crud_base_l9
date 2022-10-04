<?php 

extract($crudBaseData, EXTR_REFS);
extract($masters, EXTR_REFS);

dump('xxx');//■■■□□□■■■□□□)
//require_once $crud_base_path . 'CrudBaseHelper.php';
$cbh = new CrudBaseHelper($crudBaseData);
$ver_str = '?v=' . $this_page_version; // キャッシュ回避のためのバージョン文字列


?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>有名猫管理画面</title>
	
	<script src="{{ asset('/js/app.js') }}" defer></script>
	<script src="{{ $crud_base_js . $ver_str }}" defer></script>
	<script src="{{ asset('/js/BigCat/index.js')  . $ver_str }}" defer></script>
	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('/js/font/css/open-iconic.min.css') }}" rel="stylesheet">
	<link href="{{ $crud_base_css . $ver_str }}" rel="stylesheet">
	<link href="{{ asset('/css/common.css')  . $ver_str}}" rel="stylesheet">
	<link href="{{ asset('/css/BigCat/index.css') . $ver_str }}" rel="stylesheet">
	
</head>
<body>

@include('layouts.common_header')

<div class="container-fluid">

<div id="app"><!-- vue.jsの場所・未使用 --></div>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{ url('/') }}">ホーム</a></li>
	<li class="breadcrumb-item active" aria-current="page">有名猫管理画面</li>
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
<?php $cbh->divNewPageVarsion(); // 新バージョン通知区分を表示?>

<!-- 検索フォーム -->
<form method="GET" action="big_cat">


	<div id="ajax_login_with_cake"></div><!-- ログイン or ログアウト 　AjaxLoginWithCake.js　-->
	<div class="cb_kj_main">
	<!-- 検索条件入力フォーム -->
	<div class="form_kjs" id="big_catIndexForm" method="post" accept-charset="utf-8">
		<input type="search" placeholder="検索" name="main_search" value="{{ old('main_search', $searches['main_search'])}}" title="ネコ名、備考を部分検索します" class="form-control search_btn_x">
		<input type='button' value='検索' onclick='searchKjs()' class='search_kjs_btn btn btn-success btn-sm' />
		<div class="btn-group">
			<button type="button" class="btn btn-secondary btn-sm" title="詳細検索項目を表示する" onclick="jQuery('.cb_kj_detail').toggle(300)">詳細検索</button>
			<a href="" class="ini_rtn btn btn-primary btn-sm" title="この画面を最初に表示したときの状態に戻します。（検索状態、列並べの状態を解除）">リセット</a>
		</div>
		<div class="cb_kj_detail" style="display:none">
			<table style="width:100%"><tbody><tr>
				<td>詳細検索</td>
				<td style="text-align:right"><button type="button" class="btn btn-secondary btn-sm"  onclick="jQuery('.cb_kj_detail').toggle(300);">閉じる</button></td>
			</tr></tbody></table>
			
		<input type="search" placeholder="ID" name="id" value="{{ old('id', $searches['id']) }}" class="form-control search_btn_x">
		<input type="search" placeholder="ネコ名" name="big_cat_name" value="{{ old('big_cat_name', $searches['big_cat_name']) }}" class="form-control search_btn_x">
		<input type="search" placeholder="公開日" name="public_date" value="{{ old('public_date', $searches['public_date']) }}" class="form-control search_btn_x">
		
		<select name="big_cat_type" class="form-control search_btn_x">
				<option value=""> - 猫種別 - </option>
				@foreach ($bigCatTypeList as $big_cat_type => $big_cat_type_name)
					<option value="{{ $big_cat_type }}" @selected(old('big_cat_type', $searches['big_cat_type']) == $big_cat_type)>
						{{ $big_cat_type_name }}
					</option>
				@endforeach
		</select>
		
		<input type="search" placeholder="価格" name="price" value="{{ old('price', $searches['price']) }}" class="form-control search_btn_x">
		<input type="search" placeholder="サブスク" name="subsc_count" value="{{ old('subsc_count', $searches['subsc_count']) }}" class="form-control search_btn_x">
		<input type="search" placeholder="作業日時" name="work_dt" value="{{ old('work_dt', $searches['work_dt']) }}" class="form-control search_btn_x">
		
		<select name="big_cat_flg" class="form-control search_btn_x">
			<option value=""> - ネコフラグ - </option>
			<option value="0" @selected(old('big_cat_flg', $searches['big_cat_flg']) == 0)>OFF</option>
			<option value="1" @selected(old('big_cat_flg', $searches['big_cat_flg']) == 1)>ON</option>
		</select>
		
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
		<?php 
		

		echo "<input type='button' value='検索' onclick='searchKjs()' class='search_kjs_btn btn btn-success' />";
		
		
		?>
				<div class="kj_div" style="margin-top:5px">
					<input type="button" value="検索入力リセット" title="検索入力を初期に戻します" onclick="resetKjs()" class="btn btn-primary btn-sm" />
				</div>
				
				<input id="crud_base_json" type="hidden" value='<?php echo $crud_base_json?>' />
		</div>
		<div id="app"></div><!-- vue.js -->
	</div><!-- form_kjs -->
	</div><!-- cb_kj_main -->
	<div id="cb_func_btns" class="btn-group" >
		<button type="button" onclick="jQuery('#detail_div').toggle(300);" class="btn btn-secondary btn-sm">ツール</button>
	</div>
</form>


<div style="margin-top:0.4em;">
	
	<div class="tool_btn_w">
		<a href="neko/create" class="btn btn-success">新規登録</a>
	</div>

	<!-- CSVダウンロード機能 -->
	<div class="tool_btn_w">
		@include('layouts.csv_form', ['path_a'=>'big_cat'])
	</div>
	
	<!-- 列表示切替機能 -->
	<div class="tool_btn_w">
		<button class="btn btn-secondary" onclick="$('#csh_div_w').toggle(300);">列表示切替</button>
		<div id="csh_div_w" style="width:100vw;" >
			<div id="csh_div" ></div><!-- 列表示切替機能の各種チェックボックスの表示場所 -->
		</div>
	</div>
	
	
    <!-- 一括追加機能  -->
    <div id="crud_base_bulk_add" style="display:none"></div>
</div>


<div style="clear:both"></div>


<div style="clear:both"></div>

<div id="detail_div" style="background-color:#ebedef;padding:4px;display:none">
	
	<div id="main_tools" style="margin-bottom:10px;margin-top:4px">
		<div style="display:inline-block;width:75%; ">

			
			<button id="crud_base_bulk_add_btn" type="button" class="btn btn-secondary btn-sm" onclick="crudBase.crudBaseBulkAdd.showForm()" >一括追加</button>
			
			<!-- CrudBase設定 -->
			<div id="crud_base_config" style="display:inline-block"></div>
			
			<button id="calendar_view_k_btn" type="button" class="btn btn-secondary btn-sm" onclick="calendarViewKShow()" >カレンダーモード</button>
			
			<button type="button" class="btn btn-secondary btn-sm" onclick="sessionClear()" >セッションクリア</button>
		
			<button id="table_transform_tbl_mode" type="button" class="btn btn-secondary btn-sm" onclick="tableTransform(0)" style="display:none">一覧の変形・テーブルモード</button>	
			<button id="table_transform_div_mode" type="button" class="btn btn-secondary btn-sm" onclick="tableTransform(1)" >一覧の変形・スマホモード</button>
		</div>
		<div style="display:inline-block;text-align:right;width:24%;">
			<button type="button" class="btn btn-secondary btn-sm" onclick="jQuery('#detail_div').toggle(300);">閉じる</button>
		</div>
	</div><!-- main_tools -->
	

</div><!-- detail_div -->

<input type="hidden" id="csrf_token" value="{{ csrf_token() }}" >
<div id="test_ajax_res"></div>


<div id="new_inp_form_point"></div><!-- 新規入力フォーム表示地点 -->

<?php //$cbh->divPagenation(); // ページネーション■■■□□□■■■□□□ ?>
<div>{{$data->appends(request()->query())->links('pagination::bootstrap-4')}} </div><!-- ページネーション -->

<div id="calendar_view_k"></div>


<div id="crud_base_auto_save_msg" style="height:20px;" class="text-success"></div>

<?php if(!empty($data)){ ?>
	<button type="button" class="btn btn-warning btn-sm" onclick="newInpShow(this, 'add_to_top');">新規追加</button>
<?php } ?>


<!-- 一覧テーブル -->
<table id="big_cat_tbl" class="table table-striped table-bordered table-condensed" style="margin-bottom:0px">

<thead>
<tr>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'id', 'ID') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'big_cat_name', 'ネコ名') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'public_date', '公開日') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'big_cat_type', '有名猫種別') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'price', '価格') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'subsc_count', 'サブスク数') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'work_dt', '作業日時') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'big_cat_flg', 'ネコフラグ') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'img_fn', '画像ファイル名') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'note', '備考') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'sort_no', '順番') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'delete_flg', '無効フラグ') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'update_user_id', '更新者') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'ip_addr', 'IPアドレス') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'created_at', '生成日時') !!}</th>
	<th>{!! $cbh->sortLink($searches, 'big_cat', 'updated_at', '更新日') !!}</th>
	<th style="min-width:207px"></th>
</tr>
</thead>
<tbody>
		@foreach ($data as $ent)
			<tr>
				<!-- CBBXS-3005 -->
				<td>{{$ent->id}}</td>
				<td>{{$ent->big_cat_name}}</td>
				<td>{!! CrudBaseHelper::tdDate($ent->public_date) !!}</td>
				<td>{{ $bigCatTypeList[$ent->big_cat_type] ?? '' }}</td>
				<td>{{$ent->price}}</td>
				<td>{{$ent->subsc_count}}</td>
				<td>{!! CrudBaseHelper::tdDate($ent->work_dt) !!}</td>
				<td>{!! CrudBaseHelper::tdFlg($ent->big_cat_flg) !!}</td>
				<td>{{$ent->img_fn}}</td>
				<td>{!! CrudBaseHelper::tdNote($ent->note, 'note', 30) !!}</td>
				<td>{{$ent->sort_no}}</td>
				<td>{!! CrudBaseHelper::tdDeleteFlg($ent->delete_flg) !!}</td>
				<td>{{$ent->update_user_id}}</td>
				<td>{{$ent->ip_addr}}</td>
				<td>{{$ent->created_at}}</td>
				<td>{{$ent->updated_at}}</td>

				<!-- CBBXE -->
				<td>
					
					{!! CrudBaseHelper::rowExchangeBtn($searches) !!}<!-- 行入替ボタン -->
					<a href="neko/show?id={{$ent->id}}" class="btn btn-info btn-sm text-light">詳細</a>
					<a href="neko/edit?id={{$ent->id}}" class="btn btn-primary btn-sm">編集</a>
					{!! CrudBaseHelper::disabledBtn($searches, $ent->id) !!}<!-- 削除/削除取消ボタン（無効/有効ボタン） -->
					{!! CrudBaseHelper::destroyBtn($searches, $ent->id) !!}<!-- 抹消ボタン -->
					
					
				</td>
			</tr>
		@endforeach

</tbody>
</table>

<div>{{$data->appends(request()->query())->links('pagination::bootstrap-4')}} </div><!-- ページネーション -->

<button type="button" class="btn btn-warning btn-sm" onclick="newInpShow(this, 'add_to_bottom');">新規追加</button>	

<?php $cbh->divPwms(); // 複数有効/削除の区分を表示する ?>


<table id="crud_base_forms">

	<!-- 新規入力フォーム -->
	<tr id="ajax_crud_new_inp_form" class="crud_base_form" style="display:none;padding-bottom:60px"><td colspan='5'>
	
		<div>
			<div style="color:#3174af;float:left">新規入力</div>
			<div style="float:left;margin-left:10px">
				<button type="button"  onclick="newInpReg();" class="btn btn-success btn-sm reg_btn">登録</button>
			</div>
			<div style="float:right">
					<button type="button" class="close" aria-label="閉じる" onclick="closeForm('new_inp')" >
						<span aria-hidden="true">&times;</span>
					</button>
			</div>
		</div>
		<div style="clear:both;height:4px"></div>
		<div class="err text-danger"></div>
		
		<div style="display:none">
			<input type="hidden" name="form_type">
			<input type="hidden" name="row_index">
			<input type="hidden" name="sort_no">
		</div>
	
	
		<!-- CBBXS-2006 -->
		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >ネコ名: </div>
			<div class='cbf_input'>
				<input type="text" name="big_cat_name" class="valid " value=""  maxlength="255" title="255文字以内で入力してください" />
				<label class="text-danger" for="big_cat_name"></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >公開日: </div>
			<div class='cbf_input'>
				<input type="text" name="public_date" class="valid datepicker" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2})" title="日付形式（Y-m-d）で入力してください(例：2012-12-12)" />
				<label class="text-danger" for="public_date"></label>
			</div>
		</div>
		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >有名猫種別: </div>
			<div class='cbf_input'>
				<?php $cbh->selectX('big_cat_type',null,$bigCatTypeList,null);?>
				<label class="text-danger" for="big_cat_type"></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >価格: </div>
			<div class='cbf_input'>
				<input type="text" name="price" class="valid" value="" pattern="^[+-]?([0-9]*[.])?[0-9]+$" maxlength="11" title="数値を入力してください" />
				<label class="text-danger" for="price" ></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >サブスク数: </div>
			<div class='cbf_input'>
				<input type="text" name="subsc_count" class="valid" value="" pattern="^[+-]?([0-9]*[.])?[0-9]+$" maxlength="11" title="数値を入力してください" />
				<label class="text-danger" for="subsc_count" ></label>
			</div>
		</div>
		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >作業日時: </div>
			<div class='cbf_input'>
				<input type="text" name="work_dt" class="valid " value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
				<label class="text-danger" for="work_dt"></label>
			</div>
		</div>
		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >ネコフラグ: </div>
			<div class='cbf_input'>
				<input type="checkbox" name="big_cat_flg" class="valid"/>
				<label class="text-danger" for="big_cat_flg" ></label>
			</div>
		</div>
		<div class="cbf_inp_wrap" style="float:left">
			<div class='cbf_inp_label_long' >画像ファイル名: </div>
			<div class='cbf_input' style="width:180px;height:auto;">
				<label for="img_fn_n" class="fuk_label" >
					<input type="file" id="img_fn_n" class="img_fn" style="display:none" accept="image/*" title="画像ファイルをドラッグ＆ドロップ(複数可)"  data-inp-ex='image_fuk' data-fp='' />
					<span class='fuk_msg' style="padding:20%">画像ファイルをドラッグ＆ドロップ(複数可)</span>
				</label>
			</div>
		</div>
		<div class="cbf_inp_wrap_long">
			<div class='cbf_inp_label' >備考： </div>
			<div class='cbf_input'>
				<textarea name="note" maxlength="1000" title="1000文字以内で入力してください" style="height:100px;width:100%"></textarea>
				<label class="text-danger" for="note"></label>
			</div>
		</div>
		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >更新者: </div>
			<div class='cbf_input'>
				<input type="text" name="update_user_id" class="valid" value="" pattern="^[0-9]+$" maxlength="11" title="数値（自然数）を入力してください" />
				<label class="text-danger" for="update_user_id" ></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >更新日: </div>
			<div class='cbf_input'>
				<input type="text" name="updated_at" class="valid " value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
				<label class="text-danger" for="updated_at"></label>
			</div>
		</div>

		<!-- CBBXE -->
		
		<div style="clear:both"></div>
		<div class="cbf_inp_wrap">
			<button type="button" onclick="newInpReg();" class="btn btn-success reg_btn">登録</button>
		</div>
	</td></tr><!-- new_inp_form -->



	<!-- 編集フォーム -->
	<tr id="ajax_crud_edit_form" class="crud_base_form" style="display:none"><td colspan='5'>
		<div  style='width:100%'>
	
			<div>
				<div style="color:#3174af;float:left">編集</div>
				<div style="float:left;margin-left:10px">
					<button type="button"  onclick="editReg();" class="btn btn-success btn-sm reg_btn">登録</button>
				</div>
				<div style="float:right">
					<button type="button" class="close" aria-label="閉じる" onclick="closeForm('edit')" >
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			</div>
			<div style="clear:both;height:4px"></div>
			<div class="err text-danger"></div>
			
			<!-- CBBXS-2007 -->
			<div class="cbf_inp_wrap">
				<div class='cbf_inp' >ID: </div>
				<div class='cbf_input'>
					<span class="id"></span>
				</div>
			</div>
		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >ネコ名: </div>
			<div class='cbf_input'>
				<input type="text" name="big_cat_name" class="valid " value=""  maxlength="255" title="255文字以内で入力してください" />
				<label class="text-danger" for="big_cat_name"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >公開日: </div>
			<div class='cbf_input'>
				<input type="text" name="public_date" class="valid datepicker" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2})" title="日付形式（Y-m-d）で入力してください(例：2012-12-12)" />
				<label class="text-danger" for="public_date"></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >有名猫種別: </div>
			<div class='cbf_input'>
				<?php $cbh->selectX('big_cat_type',null,$bigCatTypeList,null);?>
				<label class="text-danger" for="big_cat_type"></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >価格: </div>
			<div class='cbf_input'>
				<input type="text" name="price" class="valid" value="" pattern="^[+-]?([0-9]*[.])?[0-9]+$" maxlength="11" title="数値を入力してください" />
				<label class="text-danger" for="price" ></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >サブスク数: </div>
			<div class='cbf_input'>
				<input type="text" name="subsc_count" class="valid " value=""  maxlength="11" title="11文字以内で入力してください" />
				<label class="text-danger" for="subsc_count"></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >作業日時: </div>
			<div class='cbf_input'>
				<input type="text" name="work_dt" class="valid " value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
				<label class="text-danger" for="work_dt"></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >ネコフラグ: </div>
			<div class='cbf_input'>
				<input type="checkbox" name="big_cat_flg" class="valid"/>
				<label class="text-danger" for="big_cat_flg" ></label>
			</div>
		</div>

		<div class="cbf_inp_wrap" style="float:left">
			<div class='cbf_inp_label_long' >画像ファイル名: </div>
			<div class='cbf_input' style="width:180px;height:auto;">
				<label for="img_fn_e" class="fuk_label" >
					<input type="file" id="img_fn_e" class="img_fn" style="display:none" accept="image/*" title="画像ファイルをドラッグ＆ドロップ(複数可)"  data-inp-ex='image_fuk' data-fp='' />
					<span class='fuk_msg' style="padding:20%">画像ファイルをドラッグ＆ドロップ(複数可)</span>
				</label>
			</div>
		</div>
		<div class="cbf_inp_wrap_long">
			<div class='cbf_inp_label' >備考： </div>
			<div class='cbf_input'>
				<textarea name="note" maxlength="1000" title="1000文字以内で入力してください" data-folding-ta="40" style="height:100px;width:100%"></textarea>
				<label class="text-danger" for="note"></label>
			</div>
		</div>

			<div class="cbf_inp_wrap">
				<div class='cbf_inp_label' >無効フラグ：</div>
				<div class='cbf_input'>
					<input type="checkbox" name="delete_flg" class="valid"  />
				</div>
			</div>
		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >更新者: </div>
			<div class='cbf_input'>
				<input type="text" name="update_user_id" class="valid" value="" pattern="^[0-9]+$" maxlength="11" title="数値（自然数）を入力してください" />
				<label class="text-danger" for="update_user_id" ></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >更新日: </div>
			<div class='cbf_input'>
				<input type="text" name="updated_at" class="valid " value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
				<label class="text-danger" for="updated_at"></label>
			</div>
		</div>


			<!-- CBBXE -->
			
			<div style="clear:both"></div>
			<div class="cbf_inp_wrap">
				<button type="button"  onclick="editReg();" class="btn btn-success reg_btn">登録</button>
			</div>
			
			<div class="cbf_inp_wrap" style="padding:5px;">
				<input type="button" value="更新情報" class="btn btn-secondary btn-sm" onclick="$('#ajax_crud_edit_form_update').toggle(300)" /><br>
				<aside id="ajax_crud_edit_form_update" style="display:none">
					更新日時: <span class="modified"></span><br>
					生成日時: <span class="created"></span><br>
					ユーザー名: <span class="update_user"></span><br>
					IPアドレス: <span class="ip_addr"></span><br>
				</aside>
			</div>
		</div>
	</td></tr>
</table>







<!-- 削除フォーム -->
<div id="ajax_crud_delete_form" class="panel panel-danger" style="display:none">

	<div class="panel-heading">
		<div class="pnl_head1">削除</div>
		<div class="pnl_head2"></div>
		<div class="pnl_head3">
			<button type="button" class="btn btn-default btn-sm" onclick="closeForm('delete')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	
	<div class="panel-body" style="min-width:300px">
	<table><tbody>

		<!-- Start ajax_form_new -->
		<tr><td>ID: </td><td>
			<span class="id"></span>
		</td></tr>
		

		<tr><td>有名猫名: </td><td>
			<span class="big_cat_name"></span>
		</td></tr>
		
		<tr><td>画像ファイル: </td><td>
			<label for="img_fn"></label><br>
			<img src="" class="img_fn" width="80" height="80" ></img>
		</td></tr>


		<!-- Start ajax_form_end -->
	</tbody></table>
	<br>
	

	<button type="button"  onclick="deleteReg();" class="btn btn-danger">
		<span class="glyphicon glyphicon-remove"></span>　削除する
	</button>
	<hr>
	
	<input type="button" value="更新情報" class="btn btn-default btn-xs" onclick="$('#ajax_crud_delete_form_update').toggle(300)" /><br>
	<aside id="ajax_crud_delete_form_update" style="display:none">
		更新日時: <span class="modified"></span><br>
		生成日時: <span class="created"></span><br>
		ユーザー名: <span class="update_user"></span><br>
		IPアドレス: <span class="ip_addr"></span><br>
		ユーザーエージェント: <span class="user_agent"></span><br>
	</aside>
	

	</div><!-- panel-body -->
</div>



<!-- 抹消フォーム -->
<div id="ajax_crud_eliminate_form" class="crud_base_form" style="display:none">

	<div class="panel-heading">
		<div class="pnl_head1">抹消</div>
		<div class="pnl_head2"></div>
		<div class="pnl_head3">
			<button type="button" class="btn btn-default btn-sm" onclick="closeForm('eliminate')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	
	<div class="panel-body" style="min-width:300px">
	<table><tbody>

		<!-- Start ajax_form_new -->
		<tr><td>ID: </td><td>
			<span class="id"></span>
		</td></tr>
		

		<tr><td>有名猫名: </td><td>
			<span class="big_cat_name"></span>
		</td></tr>


		<!-- Start ajax_form_end -->
	</tbody></table>
	<br>
	

	<button type="button"  onclick="eliminateReg();" class="btn btn-danger">
		<span class="glyphicon glyphicon-remove"></span>　抹消する
	</button>
	<hr>
	
	<input type="button" value="更新情報" class="btn btn-default btn-xs" onclick="$('#ajax_crud_eliminate_form_update').toggle(300)" /><br>
	<aside id="ajax_crud_eliminate_form_update" style="display:none">
		更新日時: <span class="modified"></span><br>
		生成日時: <span class="created"></span><br>
		ユーザー名: <span class="update_user"></span><br>
		IPアドレス: <span class="ip_addr"></span><br>
		ユーザーエージェント: <span class="user_agent"></span><br>
	</aside>
	

	</div><!-- panel-body -->
</div><br>


@include('layouts.common_footer')


</div></body>
</html>