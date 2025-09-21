<div class="header">
    <h1>برنامه نهایی هفته</h1>
    <a href="index.php?url=dashboard" class="btn btn-secondary">بازگشت به داشبورد</a>
</div>

<div class="week-navigation">
    <?php
        $prevWeek = date('Y-m-d', strtotime($data['week_start_date'] . ' -7 days'));
        $nextWeek = date('Y-m-d', strtotime($data['week_start_date'] . ' +7 days'));
    ?>
    <a href="index.php?url=schedule/index/<?php echo $prevWeek; ?>" class="btn btn-primary">&lt; هفته قبل</a>
    <h2>
        هفته از <?php echo jDateTime::date('d F Y', strtotime($data['week_start_date'])); ?>
        تا <?php echo jDateTime::date('d F Y', strtotime($data['week_end_date'])); ?>
    </h2>
    <a href="index.php?url=schedule/index/<?php echo $nextWeek; ?>" class="btn btn-primary">هفته بعد &gt;</a>
</div>

<div class="weekly-grid">
    <?php
    $days = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
    $userRole = Session::get('user_role');
    $canSeeObjective = in_array($userRole, ['director', 'assessor', 'admin']);

    for ($i = 0; $i < 7; $i++):
        $currentDayTimestamp = strtotime($data['week_start_date'] . " +$i days");
        $currentDay = date('Y-m-d', $currentDayTimestamp);
    ?>
    <div class="day-column">
        <div class="day-header"><?php echo $days[$i]; ?><br><small><?php echo jDateTime::date('Y/m/d', $currentDayTimestamp); ?></small></div>

        <?php foreach ($data['proposals'] as $proposal):
            if (date('Y-m-d', strtotime($proposal->event_datetime)) == $currentDay): ?>
            <div class="event">
                <div class="event-title"><?php echo htmlspecialchars($proposal->title); ?></div>
                <div class="event-details">
                    <p><strong>زمان:</strong> <?php echo date('H:i', strtotime($proposal->event_datetime)); ?></p>
                    <p><strong>مخاطبان:</strong> <?php echo implode('، ', array_map('htmlspecialchars', $proposal->audiences)); ?></p>
                    <p><strong>برگزارکننده:</strong> <?php echo implode('، ', array_map('htmlspecialchars', $proposal->organizers)); ?></p>
                    <p><strong>اولویت:</strong> <?php echo __($proposal->priority); ?></p>
                    <?php if ($canSeeObjective): ?>
                    <div class="objective">
                        <strong>هدف:</strong> <?php echo nl2br(htmlspecialchars($proposal->objective)); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; endforeach; ?>

    </div>
    <?php endfor; ?>
</div>
