<div class="top-bar">
    <div class="macro-plan-bar">
        <span>برنامه کلان:</span>
        <?php foreach ($data['macro_plan_events'] as $event): ?>
            <span class="macro-event-item"><?php echo htmlspecialchars($event->title); ?></span>
        <?php endforeach; ?>
        <a href="index.php?url=macroplan" class="btn btn-secondary btn-sm">مشاهده همه</a>
    </div>
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
                    <?php for ($h = 4; $h <= 22; $h++): ?>
                        <div class="time-slot"><?php echo sprintf('%02d:00', $h); ?></div>
                        <div class="time-slot"></div>
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
    </div>
</div>

<!-- Floating Action Button -->
<button id="fab-add-proposal" class="fab">+</button>

<!-- Quick Add Modal -->
<div id="quick-add-modal" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close-btn">&times;</span>
        <h3>ایجاد پیشنهاد سریع</h3>
        <form id="quick-add-form" action="index.php?url=proposals/add" method="POST">
            <div class="form-group">
                <label for="modal-title">عنوان برنامه</label>
                <input type="text" id="modal-title" name="title" required>
            </div>
            <div class="form-group">
                <label for="modal-event-datetime">زمان شروع</label>
                <input type="text" id="modal-event-datetime" name="event_datetime" data-jdp data-jdp-time required>
            </div>
            <div class="form-group">
                <label for="modal-event-end-datetime">زمان پایان</label>
                <input type="text" id="modal-event-end-datetime" name="event_end_datetime" data-jdp data-jdp-time>
            </div>
             <div class="form-group">
                <label for="modal-objective">هدف برنامه</label>
                <textarea id="modal-objective" name="objective" required></textarea>
            </div>
            <div class="form-group">
                <label>مخاطبان</label>
                <div class="multi-select-group modal-multi-select">
                    <?php if (!empty($audiences)): ?>
                        <?php foreach ($audiences as $audience): ?>
                            <label><input type="checkbox" name="audiences[]" value="<?php echo $audience->id; ?>"> <?php echo htmlspecialchars($audience->name); ?></label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label>برگزارکنندگان</label>
                 <div class="multi-select-group modal-multi-select">
                    <?php if (!empty($organizers)): ?>
                        <?php foreach ($organizers as $organizer): ?>
                            <label><input type="checkbox" name="organizers[]" value="<?php echo $organizer->id; ?>"> <?php echo htmlspecialchars($organizer->name); ?></label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="modal-priority">اولویت</label>
                <select id="modal-priority" name="priority" required>
                    <option value="medium" selected>متوسط</option>
                    <option value="high">زیاد</option>
                    <option value="low">کم</option>
                </select>
            </div>
            <input type="hidden" name="current_semester_id" value="<?php echo htmlspecialchars($current_semester->id ?? ''); ?>">
            <button type="submit" class="btn btn-primary">ثبت</button>
        </form>
    </div>
</div>
