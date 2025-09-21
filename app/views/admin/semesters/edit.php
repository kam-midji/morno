<div class="header">
    <h1>ویرایش ترم</h1>
    <a href="index.php?url=admin/semesters" class="btn btn-secondary">بازگشت به لیست</a>
</div>

<form action="index.php?url=admin/editSemester/<?php echo $data['id']; ?>" method="POST">
    <div class="form-group">
        <label for="name">نام ترم</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>
        <?php if (isset($data['errors']['name'])): ?><span class="error-text"><?php echo $data['errors']['name']; ?></span><?php endif; ?>
    </div>
    <div class="form-group">
        <label for="start_date">تاریخ شروع</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($data['start_date']); ?>" required>
        <?php if (isset($data['errors']['start_date'])): ?><span class="error-text"><?php echo $data['errors']['start_date']; ?></span><?php endif; ?>
    </div>
    <div class="form-group">
        <label for="end_date">تاریخ پایان</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($data['end_date']); ?>" required>
        <?php if (isset($data['errors']['end_date'])): ?><span class="error-text"><?php echo $data['errors']['end_date']; ?></span><?php endif; ?>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">به‌روزرسانی</button>
    </div>
</form>
