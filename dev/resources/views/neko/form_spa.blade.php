

<div id="form_spa" style="display:none">
<div style="max-width:840px;margin: auto;">

	<div class="row">
		<div class="col-md-3">
			<h2 class="text-success js_create_mode" >新規入力</h2>
			<h2 class="text-primary js_edit_mode">編集</h2>
		</div>
		<div class="col-md-9" style="text-align:right">
		
			<span class="text-danger js_valid_err_msg">エラーメッセージ</span>
			<span class="text-success js_registering_msg"  >データベースに登録中です...</span>
			<button type="button" class="btn btn-success  btn-lg js_submit_btn js_create_mode" onclick="regAction();">登録</button>
			<button type="button" class="btn btn-warning  btn-lg js_submit_btn js_edit_mode" onclick="regAction();">変更</button>
			<button type="button" class="btn btn-outline-secondary btn-lg close" aria-label="閉じる" onclick="closeForm()" >閉じる</button>
		</div>
	</div>

	<div class="err text-danger"></div>
	
	<!-- CBBXS-2007 -->
	
	<input type="hidden" name="sort_no">
	
	<div class="row js_edit_mode">
		<div class='col-md-12'>
			ID:<span data-display="id"></span>
			<input type="hidden" name="id" value=''  />
		</div>
	</div>
	
	<div class="row mt-2">
		<div class='col-md-2 ' >ネコ名</div>
		<div class='col-md-10'>
			<input type="text" name="neko_name" class="form-control form-control-lg " value=""  maxlength="255"  required title="ネコ名は255文字以内で入力してください" />
			<span class="text-danger" data-valid-err='neko_name'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2' >ネコ数値 </div>
		<div class='col-md-10'>
			<input type="number" name="neko_val" class="form-control form-control-lg " value="" pattern="^[0-9]+$"  title="ネコ数値は数値を入力してください" required />
			<span class="text-danger" data-valid-err="neko_val" ></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2' >ネコ日付 </div>
		<div class='col-md-10'>
			<input type="date" name="neko_date" class="form-control form-control-lg " value="" pattern="^[0-9]+$"  title="ネコ日付を入力してください(例→ 2023/4/1)" />
			<span class="text-danger" data-valid-err="neko_date" ></span>
		</div>
	</div>
	
	
	<div class="row mt-2">
		<div class='col-md-2' >ネコ種別 </div>
		<div class='col-md-10'>
			<select name="neko_type" class="form-control form-control-lg">
				@foreach ($nekoTypeList as $neko_type => $neko_type_name)
					<option value="{{ $neko_type }}" @selected(old('neko_type', $searches['neko_type']) == $neko_type)>
						{{ $neko_type_name }}
					</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2' >ネコ日時</div>
		<div class='col-md-10'>
			<input type="datetime-local" name="neko_dt" class="form-control form-control-lg " value=""  pattern="([0-9]{4})(\/|-)([0-9]{1,2})(\/|-)([0-9]{1,2}) \d{2}:\d{2}:\d{2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 01:02:03)" />
			<span class="text-danger" data-valid-err="neko_dt" ></span>
		</div>
	</div>
	
	<div class="row mt-2">
		<div class='col-md-2' >ネコフラグ</div>
		<div class='col-md-10'>
			<div class="form-check form-check-inline">
			  <input class="form-check-input" type="radio" name="neko_flg" id="neko_flg_at1" value="1">
			  <label class="form-check-label text-success" for="neko_flg_at1">ON </label>
			</div>
			<div class="form-check form-check-inline">
			  <input class="form-check-input" type="radio" name="neko_flg" id="neko_flg_at2" value="0">
			  <label class="form-check-label text-secondary" for="neko_flg_at2">OFF</label>
			</div>
		</div>
	</div>
	
	<div class="row mt-2" >
		<div class='col-md-3' >画像ファイル名 </div>
		<div class='col-md-9' >
			{!! $cbh->imgInput('img_fn') !!}
		</div>
	</div>
	
	<div class="row mt-2">
		<div class='col-md-2' >削除</div>
		<div class='col-md-10'>
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="" id="delete_flg" name="delete_flg">
				<label class="form-check-label" for="delete_flg">チェックすると削除扱いになります</label>
			</div>
		</div>
	</div>
	
	<div class="row mt-2">
		<div class='col-md-2' >備考 </div>
		<div class='col-md-10'>
			<textarea name="note" class="form-control form-control-lg " maxlength="2000" title="2000文字以内で入力してください" ></textarea>
			<div class="text-danger" data-valid-err="note"></div>
		</div>
	</div>
	
	<!-- CBBXE -->

	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-9" style="text-align:right">
		
			<span class="text-danger js_valid_err_msg">エラーメッセージ</span>
			<span class="text-success js_registering_msg"  >データベースに登録中です...</span>
			<button type="button" class="btn btn-success  btn-lg js_submit_btn js_create_mode" onclick="regAction();">登録</button>
			<button type="button" class="btn btn-warning  btn-lg js_submit_btn js_edit_mode" onclick="regAction();">変更</button>
			<button type="button" class="btn btn-outline-secondary btn-lg close" aria-label="閉じる" onclick="closeForm()" >閉じる</button>
		</div>
	</div>
	
	<div class="cbf_inp_wrap js_edit_mode" style="padding:5px;">
		<input type="button" value="更新情報" class="btn btn-secondary btn-sm" onclick="$('#edit_detail_info').toggle(300)" /><br>
		<aside id="edit_detail_info" style="display:none">
			<div>更新日時: <span data-display="updated_at"></span></div>
			<div>生成日時: <span data-display="created_at"></span></div>
			<div>更新ユーザー名: <span data-display="update_user"></span></div>
			<div>IPアドレス: <span data-display="ip_addr"></span></div>
		</aside>
	</div>

</div><!-- max-width -->
</div><!-- form_spa -->