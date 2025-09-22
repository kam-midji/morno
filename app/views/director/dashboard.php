<div class="header">
    <h1>داشبورد مدیر - بررسی پیشنهادات</h1>
    <div>
        <a href="index.php?url=admin/semesters" class="btn btn-secondary">مدیریت ترم‌ها</a>
        <a href="index.php?url=macroplan" class="btn btn-secondary">مدیریت برنامه کلان</a>
        <a href="index.php?url=schedule" class="btn btn-secondary">نمای برنامه نهایی</a>
    </div>
</div>

<div class="week-navigation">
    <?php
        $prevWeek = date('Y-m-d', strtotime($data['week_start_date'] . ' -7 days'));
        $nextWeek = date('Y-m-d', strtotime($data['week_start_date'] . ' +7 days'));
    ?>
    <a href="index.php?url=director/index/<?php echo $prevWeek; ?>" class="btn btn-primary">&lt; هفته قبل</a>
    <h2>
        برنامه‌های در انتظار تایید برای هفته: <?php echo jDateTime::date('d F', strtotime($data['week_start_date'])); ?>
    </h2>
    <a href="index.php?url=director/index/<?php echo $nextWeek; ?>" class="btn btn-primary">هفته بعد &gt;</a>
</div>

<ul class="proposal-list">
    <?php foreach ($data['pending_proposals'] as $proposal): ?>
    <li class="proposal-item">
        <div class="proposal-details">
            <span class="proposal-title"><?php echo htmlspecialchars($proposal->title); ?></span>
            <?php if (!empty($proposal->conflicts)): ?>
                <div class="conflict-indicator">
                    ⚠️ تداخل!
                    <div class="conflict-tooltip">
                        <strong>تداخل با:</strong>
                        <ul>
                        <?php foreach($proposal->conflicts as $conflict): ?>
                            <li><?php echo htmlspecialchars($conflict['title']) . ' (' . __($conflict['type']) . ')'; ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            <div class="proposal-meta">
                <strong>زمان:</strong> <?php echo jDateTime::date('l Y/m/d - H:i', strtotime($proposal->event_datetime)); ?> |
                <strong>ارسال شده توسط:</strong> <?php echo htmlspecialchars($proposal->author_name); ?> |
                <strong>اولویت:</strong> <?php echo __($proposal->priority); ?>
            </div>
        </div>
        <div class="proposal-actions">
            <form action="index.php?url=director/approve/<?php echo $proposal->id; ?>" method="POST">
                <button type="submit" class="btn btn-success">تایید</button>
            </form>
        </div>
    </li>
    <?php endforeach; ?>
    <?php if (empty($data['pending_proposals'])): ?>
        <p>هیچ پیشنهاد در انتظار تاییدی برای این هفته وجود ندارد.</p>
    <?php endif; ?>
</ul>
