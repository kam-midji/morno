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

        <div class="calendar-grid-container">
            <div class="calendar-grid">
                <!-- Time Slots Column -->
                <div class="time-slots">
                    <div class="day-header-empty"></div>
                    <?php for ($h = 7; $h < 21; $h++): ?>
                        <div class="time-slot"><?php echo sprintf('%02d:00', $h); ?></div>
                        <div class="time-slot"><?php echo sprintf('%02d:30', $h); ?></div>
                    <?php endfor; ?>
                </div>

                <!-- Day Columns -->
                <?php
                $days = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
                for ($i = 0; $i < 7; $i++):
                    $currentDayTimestamp = strtotime($data['week_start_date'] . " +$i days");
                ?>
                <div class="day-column">
                    <div class="day-header"><?php echo $days[$i]; ?><br><small><?php echo jDateTime::date('m/d', $currentDayTimestamp); ?></small></div>
                </div>
                <?php endfor; ?>

                <!-- Events -->
                <?php
                $all_proposals = array_merge($data['approved_proposals'], $data['pending_proposals']);
                foreach($all_proposals as $proposal):
                    $style = "grid-column: " . ($proposal->grid_column + 1) . "; grid-row: " . $proposal->grid_row_start . " / span " . $proposal->grid_row_span . ";";
                    $class = $proposal->status == 'approved' ? 'event-approved' : 'event-pending';
                ?>
                    <div class="event <?php echo $class; ?>" style="<?php echo $style; ?>">
                        <div class="event-title"><?php echo htmlspecialchars($proposal->title); ?></div>
                        <div class="event-time">
                            <?php echo date('H:i', strtotime($proposal->event_datetime)); ?>
                            <?php if(!empty($proposal->event_end_datetime)) echo ' - ' . date('H:i', strtotime($proposal->event_end_datetime)); ?>
                        </div>
                        <?php if($proposal->status == 'pending'): ?>
                            <a href="index.php?url=proposals/edit/<?php echo $proposal->id; ?>">ویرایش</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Agenda View for Mobile -->
        <div class="agenda-view">
             <?php
                $all_proposals = array_merge($data['approved_proposals'], $data['pending_proposals']);
                usort($all_proposals, function($a, $b) {
                    return strtotime($a->event_datetime) - strtotime($b->event_datetime);
                });
            ?>
            <?php if(empty($all_proposals)): ?>
                <p>هیچ برنامه‌ای برای این هفته وجود ندارد.</p>
            <?php else: ?>
                <?php foreach ($all_proposals as $event): ?>
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

<!-- Floating Action Button -->
<button id="fab-add-proposal" class="fab">+</button>
