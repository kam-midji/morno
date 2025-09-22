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
        <tr class="clickable-row"
            data-title="<?php echo htmlspecialchars($proposal->title); ?>"
            data-author="<?php echo htmlspecialchars($proposal->author_name); ?>"
            data-time="<?php echo jDateTime::date('l Y/m/d - H:i', strtotime($proposal->event_datetime)); ?>"
            data-objective="<?php echo htmlspecialchars($proposal->objective); ?>">
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

<!-- Details Modal -->
<div id="details-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="modal-close-btn">&times;</span>
        <h2 id="modal-title"></h2>
        <p><strong>ارسال کننده:</strong> <span id="modal-author"></span></p>
        <p><strong>زمان:</strong> <span id="modal-time"></span></p>
        <div class="objective">
            <strong>هدف:</strong>
            <p id="modal-objective"></p>
        </div>
    </div>
</div>

<style>
.status-pending { color: var(--warning-color); font-weight: bold; }
.clickable-row { cursor: pointer; }
.clickable-row:hover { background-color: #f0f0f0; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('details-modal');
    const closeModalBtn = modal.querySelector('.modal-close-btn');
    const rows = document.querySelectorAll('.clickable-row');

    rows.forEach(row => {
        row.addEventListener('click', () => {
            document.getElementById('modal-title').innerText = row.dataset.title;
            document.getElementById('modal-author').innerText = row.dataset.author;
            document.getElementById('modal-time').innerText = row.dataset.time;
            document.getElementById('modal-objective').innerText = row.dataset.objective;
            modal.style.display = 'flex';
        });
    });

    closeModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>
