<div class="header">
    <h1>مدیریت برنامه کلان (ترم: <?php echo htmlspecialchars($data['semester']->name); ?>)</h1>
    <div>
        <a href="index.php?url=macroplan/add" class="btn btn-primary">افزودن رویداد جدید</a>
        <a href="index.php?url=dashboard" class="btn btn-secondary">بازگشت به داشبورد</a>
    </div>
</div>

<div class="table-responsive-wrapper">
<table>
    <thead>
        <tr>
            <th>عنوان رویداد</th>
            <th>توضیحات</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['events'] as $event): ?>
        <tr>
            <td><?php echo htmlspecialchars($event->title); ?></td>
            <td><?php echo nl2br(htmlspecialchars($event->description)); ?></td>
            <td class="actions">
                <a href="index.php?url=macroplan/edit/<?php echo $event->id; ?>" class="btn btn-secondary">ویرایش</a>
                <form action="index.php?url=macroplan/delete/<?php echo $event->id; ?>" method="POST" style="display:inline;">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('آیا از حذف این مورد اطمینان دارید؟');">حذف</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
