<div class="header">
    <h1>افزودن برگزارکننده جدید</h1>
    <a href="index.php?url=admin/organizers" class="btn btn-secondary">بازگشت به لیست</a>
</div>

<form action="index.php?url=admin/addOrganizer" method="POST">
    <div class="form-group">
        <label for="name">نام برگزارکننده</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>
        <?php if (!empty($data['error'])): ?><span class="error-text"><?php echo $data['error']; ?></span><?php endif; ?>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">ذخیره</button>
    </div>
</form>
