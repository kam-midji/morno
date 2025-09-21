<div class="header">
    <h1>نمای ارزیاب - لیست تمام پیشنهادات</h1>
    <a href="index.php?url=users/logout" class="btn btn-secondary"><?php echo __('logout'); ?></a>
</div>

<div class="table-responsive-wrapper">
<table>
    <thead>
        <tr>
            <th>عنوان</th>
            <th>ارسال کننده</th>
            <th>زمان برنامه</th>
            <th>وضعیت</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['proposals'] as $proposal): ?>
        <tr>
            <td><?php echo htmlspecialchars($proposal->title); ?></td>
            <td><?php echo htmlspecialchars($proposal->author_name); ?></td>
            <td><?php echo jDateTime::date('Y/m/d H:i', strtotime($proposal->event_datetime)); ?></td>
            <td>
                <?php if ($proposal->status == 'approved'): ?>
                    <span class="status-active"><?php echo __('approved'); ?></span>
                <?php else: ?>
                    <span class="status-pending"><?php echo __('pending'); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($data['proposals'])): ?>
            <tr>
                <td colspan="4" style="text-align: center;">هیچ پیشنهادی برای نمایش وجود ندارد.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<style>
.status-pending { color: #ffc107; font-weight: bold; }
</style>
