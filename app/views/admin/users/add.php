<div class="header">
    <h1>افزودن کاربر جدید</h1>
    <a href="index.php?url=admin/users" class="btn btn-secondary">بازگشت به لیست</a>
</div>

<form action="index.php?url=admin/addUser" method="POST">
    <div class="form-group">
        <label for="full_name">نام کامل</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($data['full_name']); ?>" required>
        <?php if (isset($data['errors']['full_name'])): ?><span class="error-text"><?php echo $data['errors']['full_name']; ?></span><?php endif; ?>
    </div>
    <div class="form-group">
        <label for="username">نام کاربری</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>
            <?php if (isset($data['errors']['username'])): ?><span class="error-text"><?php echo $data['errors']['username']; ?></span><?php endif; ?>
    </div>
    <div class="form-group">
        <label for="password">رمز عبور</label>
        <input type="password" id="password" name="password" required>
            <?php if (isset($data['errors']['password'])): ?><span class="error-text"><?php echo $data['errors']['password']; ?></span><?php endif; ?>
    </div>
    <div class="form-group">
        <label for="role">نقش</label>
        <select id="role" name="role">
            <option value="user" <?php echo ($data['role'] == 'user') ? 'selected' : ''; ?>><?php echo __('user'); ?></option>
            <option value="assessor" <?php echo ($data['role'] == 'assessor') ? 'selected' : ''; ?>><?php echo __('assessor'); ?></option>
            <option value="director" <?php echo ($data['role'] == 'director') ? 'selected' : ''; ?>><?php echo __('director'); ?></option>
            <option value="admin" <?php echo ($data['role'] == 'admin') ? 'selected' : ''; ?>><?php echo __('admin'); ?></option>
        </select>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">ذخیره کاربر</button>
    </div>
</form>
