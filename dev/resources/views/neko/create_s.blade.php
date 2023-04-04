<form>
  <div class="mb-3">
    <label for="nekoVal" class="form-label">猫の値</label>
    <input type="number" class="form-control" id="nekoVal" name="neko_val" required>
  </div>
  <div class="mb-3">
    <label for="nekoName" class="form-label">猫の名前</label>
    <input type="text" class="form-control" id="nekoName" name="neko_name" required>
  </div>
  <div class="mb-3">
    <label for="nekoDate" class="form-label">猫の誕生日</label>
    <input type="date" class="form-control" id="nekoDate" name="neko_date" required>
  </div>
  <div class="mb-3">
    <label for="nekoType" class="form-label">猫の種類</label>
    <select class="form-select" id="nekoType" name="neko_type" required>
      <option value="">選択してください</option>
      <option value="1">アビシニアン</option>
      <option value="2">スコティッシュフォールド</option>
      <option value="3">マンチカン</option>
      <option value="4">ベンガル</option>
      <option value="5">ロシアンブルー</option>
    </select>
  </div>
  <div class="mb-3">
    <label for="nekoDt" class="form-label">猫の登録日時</label>
    <input type="datetime-local" class="form-control" id="nekoDt" name="neko_dt" required>
  </div>
  <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="nekoFlg" name="neko_flg">
    <label class="form-check-label" for="nekoFlg">ネコフラグ</label>
  </div>
  <div class="mb-3">
    <label for="imgFn" class="form-label">画像ファイル名</label>
    <input type="text" class="form-control" id="imgFn" name="img_fn" required>
  </div>
  <div class="mb-3">
    <label for="note" class="form-label">備考</label>
    <textarea class="form-control" id="note" name="note"></textarea>
  </div>
  <div class="mb-3">
    <label for="sortNo" class="form-label">順番</label>
    <input type="number" class="form-control" id="sortNo" name="sort_no" required>
  </div>
  <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="deleteFlg" name="delete_flg">
    <label class="form-check-label" for="deleteFlg">無効フラグ</label>
  </div>
  <div class="
