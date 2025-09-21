<div class="header">
    <h1>مدیریت ترم‌ها</h1>
    <a href="index.php?url=admin/addSemester" class="btn btn-primary">افزودن ترم جدید</a>
</div>

<table>
    <thead>
        <tr>
            <th>نام ترم</th>
            <th>تاریخ شروع</th>
            <th>تاریخ پایان</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['semesters'] as $semester): ?>
        <tr>
            <td><?php echo htmlspecialchars($semester->name); ?></td>
            <td><?php echo jDateTime::date('Y/m/d', strtotime($semester->start_date)); ?></td>
            <td><?php echo jDateTime::date('Y/m/d', strtotime($semester->end_date)); ?></td>
            <td>
                <?php if ($semester->is_archived): ?>
                    <span class="status-archived">آرشیوشده</span>
                <?php else: ?>
                    <span class="status-active">فعال</span>
                <?php endif; ?>
            </td>
            <td class="actions">
                <a href="index.php?url=admin/editSemester/<?php echo $semester->id; ?>" class="btn btn-secondary">ویرایش</a>
                <?php if (!$semester->is_archived): ?>
                <form action="index.php?url=admin/archiveSemester/<?php echo $semester->id; ?>" method="POST" style="display:inline;">
                    <button type="submit" class="btn btn-warning" onclick="return confirm('آیا از آرشیو کردن این ترم اطمینان دارید؟ برنامه‌های این ترم دیگر قابل ویرایش نخواهند بود.');">آرشیو</button>
                </form>
                <?php else: ?>
                    <span class="btn btn-disabled">آرشیو</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
