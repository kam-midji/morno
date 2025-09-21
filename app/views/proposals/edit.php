<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش پیشنهاد - <?php echo __('appName'); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/jalalidatepicker.min.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>ویرایش پیشنهاد</h1>
        <a href="index.php?url=dashboard" class="btn btn-secondary">بازگشت به داشبورد</a>
    </div>

    <form action="index.php?url=proposals/edit/<?php echo $data['id']; ?>" method="POST">
        <div class="form-group">
            <label for="title"><?php echo __('proposal_title'); ?></label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($data['title'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="event_datetime"><?php echo __('event_time'); ?></label>
            <input type="text" id="event_datetime" name="event_datetime" value="<?php echo htmlspecialchars($data['event_datetime_jalali'] ?? ''); ?>" data-jdp data-jdp-time required>
        </div>

        <div class="form-group">
            <label><?php echo __('audience'); ?></label>
            <div class="multi-select-group">
                <?php foreach ($data['all_audiences'] as $audience): ?>
                    <label><input type="checkbox" name="audiences[]" value="<?php echo $audience->id; ?>" <?php echo in_array($audience->id, $data['selected_audiences'] ?? []) ? 'checked' : ''; ?>> <?php echo htmlspecialchars($audience->name); ?></label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label><?php echo __('organizer'); ?></label>
            <div class="multi-select-group">
                <?php foreach ($data['all_organizers'] as $organizer): ?>
                    <label><input type="checkbox" name="organizers[]" value="<?php echo $organizer->id; ?>" <?php echo in_array($organizer->id, $data['selected_organizers'] ?? []) ? 'checked' : ''; ?>> <?php echo htmlspecialchars($organizer->name); ?></label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="objective"><?php echo __('objective'); ?></label>
            <textarea id="objective" name="objective" required><?php echo htmlspecialchars($data['objective'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="priority"><?php echo __('priority'); ?></label>
            <select id="priority" name="priority">
                <option value="low" <?php echo ($data['priority'] == 'low') ? 'selected' : ''; ?>><?php echo __('low'); ?></option>
                <option value="medium" <?php echo ($data['priority'] == 'medium') ? 'selected' : ''; ?>><?php echo __('medium'); ?></option>
                <option value="high" <?php echo ($data['priority'] == 'high') ? 'selected' : ''; ?>><?php echo __('high'); ?></option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">به‌روزرسانی</button>
        </div>
    </form>
</div>
<script type="text/javascript" src="assets/js/jalalidatepicker.min.js"></script>
<script type="text/javascript">
    jalaliDatepicker.startWatch({ time: true, persianDigits: true, format: 'YYYY/MM/DD HH:mm:ss' });
</script>
</body>
</html>
