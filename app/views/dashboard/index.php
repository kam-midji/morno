<div class="header">
    <h1><?php echo __('appName'); ?> - <?php echo htmlspecialchars(Session::get('username')); ?></h1>
    <a href="index.php?url=users/logout" class="btn btn-secondary"><?php echo __('logout'); ?></a>
</div>

<div class="main-container">
    <div class="schedule-container">
        <div class="week-navigation">
            <?php
                $prevWeek = date('Y-m-d', strtotime($data['week_start_date'] . ' -7 days'));
                $nextWeek = date('Y-m-d', strtotime($data['week_start_date'] . ' +7 days'));
            ?>
            <a href="index.php?url=dashboard/index/<?php echo $prevWeek; ?>" class="btn btn-primary">&lt; هفته قبل</a>
            <h2>
                هفته از <?php echo jDateTime::date('d F Y', strtotime($data['week_start_date'])); ?>
                تا <?php echo jDateTime::date('d F Y', strtotime($data['week_end_date'])); ?>
            </h2>
            <a href="index.php?url=dashboard/index/<?php echo $nextWeek; ?>" class="btn btn-primary">هفته بعد &gt;</a>
        </div>

        <!-- View Toggle for Mobile -->
        <div class="view-toggle">
            <button id="show-grid-btn" class="btn btn-secondary">نمایش جدولی</button>
            <button id="show-agenda-btn" class="btn btn-primary">نمایش لیستی</button>
        </div>

        <!-- Weekly Grid View (Default for Desktop) -->
        <div class="weekly-grid">
            <?php
            $days = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
            for ($i = 0; $i < 7; $i++):
                $currentDayTimestamp = strtotime($data['week_start_date'] . " +$i days");
                $currentDay = date('Y-m-d', $currentDayTimestamp);
            ?>
            <div class="day-column">
                <div class="day-header"><?php echo $days[$i]; ?><br><small><?php echo jDateTime::date('Y/m/d', $currentDayTimestamp); ?></small></div>

                <?php foreach ($data['approved_proposals'] as $proposal):
                    if (date('Y-m-d', strtotime($proposal->event_datetime)) == $currentDay): ?>
                    <div class="event event-approved">
                        <div class="event-title"><?php echo htmlspecialchars($proposal->title); ?></div>
                        <div class="event-time"><?php echo date('H:i', strtotime($proposal->event_datetime)); ?></div>
                    </div>
                <?php endif; endforeach; ?>

                <?php foreach ($data['pending_proposals'] as $proposal):
                        if (date('Y-m-d', strtotime($proposal->event_datetime)) == $currentDay): ?>
                    <div class="event event-pending">
                        <div class="event-title"><?php echo htmlspecialchars($proposal->title); ?> (در انتظار)</div>
                        <div class="event-time"><?php echo date('H:i', strtotime($proposal->event_datetime)); ?></div>
                        <div><a href="index.php?url=proposals/edit/<?php echo $proposal->id; ?>">ویرایش</a></div>
                    </div>
                <?php endif; endforeach; ?>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Agenda List View (Default for Mobile) -->
        <div class="agenda-view">
            <?php
                $all_events = array_merge($data['approved_proposals'], $data['pending_proposals']);
                usort($all_events, function($a, $b) {
                    return strtotime($a->event_datetime) - strtotime($b->event_datetime);
                });
            ?>
            <?php if(empty($all_events)): ?>
                <p>هیچ برنامه‌ای برای این هفته وجود ندارد.</p>
            <?php else: ?>
                <?php foreach ($all_events as $event): ?>
                    <div class="event <?php echo $event->status == 'approved' ? 'event-approved' : 'event-pending'; ?>">
                        <div class="event-title"><?php echo htmlspecialchars($event->title); ?> <?php echo $event->status == 'pending' ? '(در انتظار)' : ''; ?></div>
                        <div class="event-time">
                            <?php echo jDateTime::date('l Y/m/d - H:i', strtotime($event->event_datetime)); ?>
                        </div>
                         <?php if($event->status == 'pending'): ?>
                            <div><a href="index.php?url=proposals/edit/<?php echo $event->id; ?>">ویرایش</a></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="sidebar">
        <h3>برنامه کلان</h3>
        <?php foreach ($data['macro_plan_events'] as $event): ?>
            <div class="macro-event">
                <div class="macro-event-title"><?php echo htmlspecialchars($event->title); ?></div>
                <div class="macro-event-desc"><?php echo htmlspecialchars($event->description); ?></div>
            </div>
        <?php endforeach; ?>
        <hr>
        <a href="index.php?url=proposals/add" class="btn btn-primary" style="width: 100%; text-align: center; margin-top: 20px;">+ ایجاد پیشنهاد جدید</a>
    </div>
</div>
