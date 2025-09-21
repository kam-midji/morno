<div class="header">
    <h1>مدیریت برگزارکنندگان</h1>
    <a href="index.php?url=admin/addOrganizer" class="btn btn-primary">افزودن برگزارکننده جدید</a>
</div>

<table>
    <thead>
        <tr>
            <th>شناسه</th>
            <th>نام برگزارکننده</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['organizers'] as $organizer): ?>
        <tr>
            <td><?php echo $organizer->id; ?></td>
            <td><?php echo htmlspecialchars($organizer->name); ?></td>
            <td class="actions">
                <a href="index.php?url=admin/editOrganizer/<?php echo $organizer->id; ?>" class="btn btn-secondary">ویرایش</a>
                <a href="index.php?url=admin/deleteOrganizer/<?php echo $organizer->id; ?>" class="btn btn-danger" onclick="return confirm('آیا از حذف این مورد اطمینان دارید؟');">حذف</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
