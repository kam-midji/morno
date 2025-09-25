<div class="container">
    <div class="header">
        <h1>افزودن رویداد جدید</h1>
        <a href="index.php?url=macroplan" class="btn btn-secondary">بازگشت به لیست</a>
    </div>

    <form action="index.php?url=macroplan/add" method="POST">
        <div class="form-group">
            <label for="title">عنوان رویداد</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="event_date">تاریخ رویداد</label>
            <input type="text" id="event_date" name="event_date" data-jdp-date-only required>
        </div>
        <div class="form-group">
            <label for="description">توضیحات/نکات</label>
            <textarea id="description" name="description"></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">ذخیره رویداد</button>
        </div>
    </form>
</div>