<form>
  <div class="mb-3">
    <label for="neko_val" class="form-label">Neko Val</label>
    <input type="number" class="form-control" id="neko_val" name="neko_val" required>
  </div>
  <div class="mb-3">
    <label for="neko_name" class="form-label">Neko Name</label>
    <input type="text" class="form-control" id="neko_name" name="neko_name" required>
  </div>
  <div class="mb-3">
    <label for="neko_date" class="form-label">Neko Date</label>
    <input type="date" class="form-control" id="neko_date" name="neko_date" required>
  </div>
  <div class="mb-3">
    <label for="neko_type" class="form-label">Neko Type</label>
    <select class="form-select" id="neko_type" name="neko_type" required>
      <option value="">Select a neko type</option>
      <option value="1">Type 1</option>
      <option value="2">Type 2</option>
      <option value="3">Type 3</option>
    </select>
  </div>
  <div class="mb-3">
    <label for="neko_dt" class="form-label">Neko DT</label>
    <input type="datetime-local" class="form-control" id="neko_dt" name="neko_dt" required>
  </div>
  <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="neko_flg" name="neko_flg">
    <label class="form-check-label" for="neko_flg">Neko Flg</label>
  </div>
  <div class="mb-3">
    <label for="img_fn" class="form-label">Image Filename</label>
    <input type="text" class="form-control" id="img_fn" name="img_fn">
  </div>
  <div class="mb-3">
    <label for="note" class="form-label">Note</label>
    <textarea class="form-control" id="note" name="note" rows="3"></textarea>
  </div>
  <div class="mb-3">
    <label for="sort_no" class="form-label">Sort No</label>
    <input type="number" class="form-control" id="sort_no" name="sort_no" required>
  </div>
  <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="delete_flg" name="delete_flg">
    <label class="form-check-label" for="delete_flg">Delete Flg</label>
  </div>
  <input type="hidden" id="update_user_id" name="update_user_id" value="">
  <input type="hidden" id="ip_addr" name="ip_addr" value="">
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
