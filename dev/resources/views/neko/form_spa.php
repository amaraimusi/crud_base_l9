
<div id="form_spa" style="display:none">
	<div  style='width:100%'>

		<div>
			<div style="color:#3174af;float:left">編集</div>
			<div style="float:left;margin-left:10px">
				<button type="button"  onclick="editReg();" class="btn btn-success btn-sm reg_btn">登録</button>
			</div>
			<div style="float:right">
				<button type="button" class="btn btn-secondary close" aria-label="閉じる" onclick="closeForm()" >閉じる</button>
			</div>
		</div>
		<div style="clear:both;height:4px"></div>
		<div class="err text-danger"></div>
		
		<!-- CBBXS-2007 -->
		
		<div style="display:none">
			<input type="hidden" name="sort_no">
		</div>
		
		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >ID: </div>
			<div class='cbf_input'>
				<span class="id"></span>
			</div>
		</div>
		
		<div class="cbf_inp_wrap_long">
			<div class='cbf_inp' >ネコ名: </div>
			<div class='cbf_input'>
				<input type="text" name="neko_name" class="valid " value=""  maxlength="255" title="255文字以内で入力してください" />
				<label class="text-danger" for="neko_name"></label>
			</div>
		</div>
		
		<div class="cbf_inp_wrap" style="float:left">
			<div class='cbf_inp_label_long' >画像ファイル名: </div>
			<div class='cbf_input' style="width:180px;height:auto;">
				<label for="img_fn_e" class="fuk_label" >
					<input type="file" id="img_fn_e" class="img_fn" style="display:none" accept="image/*" title="画像ファイルをドラッグ＆ドロップ(複数可)" data-inp-ex='image_fuk' data-fp='' />
					<span class='fuk_msg' style="padding:20%">画像ファイルをドラッグ＆ドロップ(複数可)</span>
				</label>
			</div>
		</div>
	
		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >ネコ数値: </div>
			<div class='cbf_input'>
				<input type="text" name="neko_val" class="valid" value="" pattern="^[0-9]+$" maxlength="11" title="数値を入力してください" />
				<label class="text-danger" for="neko_val" ></label>
			</div>
		</div>
		
		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >ネコ日付: </div>
			<div class='cbf_input'>
				<input type="text" name="neko_date" class="valid datepicker" value=""  pattern="([0-9]{4})(\/|-)([0-9]{1,2})(\/|-)([0-9]{1,2})" title="日付形式（Y-m-d）で入力してください(例：2012-12-12)" />
				<label class="text-danger" for="neko_date"></label>
			</div>
		</div>
		
		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >ネコ種別: </div>
			<div class='cbf_input'>
				<?php //$cbh->selectX('neko_group',null,$nekoGroupList,null);?>
				<label class="text-danger" for="neko_group"></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >ネコ日時: </div>
			<div class='cbf_input'>
				<input type="text" name="neko_dt" class="valid " value=""  pattern="([0-9]{4})(\/|-)([0-9]{1,2})(\/|-)([0-9]{1,2}) \d{2}:\d{2}:\d{2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
				<label class="text-danger" for="neko_dt"></label>
			</div>
		</div>
		
		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >ネコフラグ: </div>
			<div class='cbf_input'>
				<input type="checkbox" name="neko_flg" class="valid"/>
				<label class="text-danger" for="neko_flg" ></label>
			</div>
		</div>
		
		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >削除：</div>
			<div class='cbf_input'>
				<input type="checkbox" name="delete_flg" class="valid"  />
			</div>
		</div>
		
		<div class="cbf_inp_wrap_long">
			<div class='cbf_inp_label' >備考： </div>
			<div class='cbf_input'>
				<textarea name="note" maxlength="1000" title="1000文字以内で入力してください" data-folding-ta="40" style="height:100px;width:100%"></textarea>
				<label class="text-danger" for="note"></label>
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
</div><!-- form_spa -->