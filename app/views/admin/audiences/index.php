<div class="header">
    <h1>مدیریت مخاطبان</h1>
    <a href="index.php?url=admin/addAudience" class="btn btn-primary">افزودن مخاطب جدید</a>
</div>

<table>
    <thead>
        <tr>
            <th>شناسه</th>
            <th>نام مخاطب</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['audiences'] as $audience): ?>
        <tr>
            <td><?php echo $audience->id; ?></td>
            <td><?php echo htmlspecialchars($audience->name); ?></td>
            <td class="actions">
                <a href="index.php?url=admin/editAudience/<?php echo $audience->id; ?>" class="btn btn-secondary">ویرایش</a>
                <a href="index.php?url=admin/deleteAudience/<?php echo $audience->id; ?>" class="btn btn-danger" onclick="return confirm('آیا از حذف این مورد اطمینان دارید؟');">حذف</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
