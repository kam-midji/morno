<div class="container">
    <div class="header">
        <h1>ویرایش پیشنهاد</h1>
        <a href="index.php?url=dashboard" class="btn btn-secondary">بازگشت به داشبورد</a>
    </div>

    <form action="index.php?url=proposals/edit/<?php echo $data['id']; ?>" method="POST">
        <div class="form-group">
            <label for="title">عنوان</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" required>
        </div>

        <div class="form-group-inline">
            <div class="form-group">
                <label for="start_date">تاریخ شروع</label>
                <input type="text" id="start_date" name="start_date" data-jdp value="<?php echo explode(' ', $data['event_datetime_jalali'])[0]; ?>" required>
            </div>
            <div class="form-group">
                <label for="start_time">زمان شروع</label>
                <input type="text" id="start_time" name="start_time" data-jdp-time-only value="<?php echo explode(' ', $data['event_datetime_jalali'])[1]; ?>" required>
            </div>
        </div>

        <div class="form-group-inline">
            <div class="form-group">
                <label for="end_date">تاریخ پایان</label>
                <input type="text" id="end_date" name="end_date" data-jdp value="<?php echo !empty($data['event_end_datetime_jalali']) ? explode(' ', $data['event_end_datetime_jalali'])[0] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="end_time">زمان پایان</label>
                <input type="text" id="end_time" name="end_time" data-jdp-time-only value="<?php echo !empty($data['event_end_datetime_jalali']) ? explode(' ', $data['event_end_datetime_jalali'])[1] : ''; ?>">
            </div>
        </div>

        <div class="form-group">
            <label>مخاطبان</label>
            <div class="multi-select-group">
                <?php foreach ($data['all_audiences'] as $audience): ?>
                    <label><input type="checkbox" name="audiences[]" value="<?php echo $audience->id; ?>" <?php echo in_array($audience->id, $data['selected_audiences']) ? 'checked' : ''; ?>> <?php echo htmlspecialchars($audience->name); ?></label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>برگزارکنندگان</label>
            <div class="multi-select-group">
                <?php foreach ($data['all_organizers'] as $organizer): ?>
                    <label><input type="checkbox" name="organizers[]" value="<?php echo $organizer->id; ?>" <?php echo in_array($organizer->id, $data['selected_organizers']) ? 'checked' : ''; ?>> <?php echo htmlspecialchars($organizer->name); ?></label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="objective">اهداف</label>
            <textarea id="objective" name="objective" required><?php echo htmlspecialchars($data['objective']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="priority">اولویت</label>
            <select id="priority" name="priority">
                <option value="low" <?php echo $data['priority'] == 'low' ? 'selected' : ''; ?>>کم</option>
                <option value="medium" <?php echo $data['priority'] == 'medium' ? 'selected' : ''; ?>>متوسط</option>
                <option value="high" <?php echo $data['priority'] == 'high' ? 'selected' : ''; ?>>زیاد</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">به‌روزرسانی</button>
        </div>
    </form>
</div>