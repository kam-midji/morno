<div class="header">
    <h1>مدیریت کاربران</h1>
    <a href="index.php?url=admin/addUser" class="btn btn-primary">افزودن کاربر جدید</a>
</div>

<table>
    <thead>
        <tr>
            <th>نام کامل</th>
            <th>نام کاربری</th>
            <th>نقش</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['users'] as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user->full_name); ?></td>
            <td><?php echo htmlspecialchars($user->username); ?></td>
            <td><?php echo __($user->role); ?></td>
            <td class="actions">
                <a href="index.php?url=admin/editUser/<?php echo $user->id; ?>" class="btn btn-secondary">ویرایش</a>
                <a href="index.php?url=admin/deleteUser/<?php echo $user->id; ?>" class="btn btn-danger" onclick="return confirm('آیا از حذف این کاربر اطمینان دارید؟');">حذف</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
