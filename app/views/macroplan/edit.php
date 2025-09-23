<div class="header">
    <h1>ویرایش رویداد</h1>
    <a href="index.php?url=macroplan" class="btn btn-secondary">بازگشت به لیست</a>
</div>

<form action="index.php?url=macroplan/edit/<?php echo $data['id']; ?>" method="POST">
    <div class="form-group">
        <label for="title">عنوان رویداد</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" required>
    </div>
    <div class="form-group">
        <label for="event_date">تاریخ رویداد</label>
        <input type="text" id="event_date" name="event_date" data-jdp-date-only value="<?php echo htmlspecialchars($data['event_date_jalali']); ?>" required>
    </div>
    <div class="form-group">
        <label for="description">توضیحات/نکات</label>
        <textarea id="description" name="description"><?php echo htmlspecialchars($data['description']); ?></textarea>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">به‌روزرسانی رویداد</button>
    </div>
</form>
