<div class="header">
    <h1>ویرایش مخاطب</h1>
    <a href="index.php?url=admin/audiences" class="btn btn-secondary">بازگشت به لیست</a>
</div>

<form action="index.php?url=admin/editAudience/<?php echo $data['id']; ?>" method="POST">
    <div class="form-group">
        <label for="name">نام مخاطب</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>
        <?php if (!empty($data['error'])): ?><span class="error-text"><?php echo $data['error']; ?></span><?php endif; ?>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">به‌روزرسانی</button>
    </div>
</form>
