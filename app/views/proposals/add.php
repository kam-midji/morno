<div class="header">
    <h1><?php echo __('create_proposal'); ?></h1>
    <a href="index.php?url=dashboard" class="btn btn-secondary">بازگشت به داشبورد</a>
</div>

<form action="index.php?url=proposals/add" method="POST">
    <input type="hidden" name="current_semester_id" value="<?php echo $data['current_semester_id']; ?>">
    <div class="form-group">
        <label for="title"><?php echo __('proposal_title'); ?></label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($data['title'] ?? ''); ?>" required>
        <?php if (isset($data['errors']['title'])): ?><span class="error-text"><?php echo $data['errors']['title']; ?></span><?php endif; ?>
    </div>

            <div class="form-group-inline">
                <div class="form-group">
                    <label for="start_date">تاریخ شروع</label>
                    <input type="text" id="start_date" name="start_date" data-jdp required>
                </div>
                <div class="form-group">
                    <label for="start_time">زمان شروع</label>
                    <input type="text" id="start_time" name="start_time" data-jdp-time-only placeholder="HH:MM" required>
                </div>
    </div>
             <?php if (isset($data['errors']['event_datetime'])): ?><span class="error-text"><?php echo $data['errors']['event_datetime']; ?></span><?php endif; ?>

            <div class="form-group-inline">
                <div class="form-group">
                    <label for="end_date">تاریخ پایان</label>
                    <input type="text" id="end_date" name="end_date" data-jdp>
                </div>
                <div class="form-group">
                    <label for="end_time">زمان پایان</label>
                    <input type="text" id="end_time" name="end_time" data-jdp-time-only placeholder="HH:MM">
                </div>
            </div>
             <?php if (isset($data['errors']['event_end_datetime'])): ?><span class="error-text"><?php echo $data['errors']['event_end_datetime']; ?></span><?php endif; ?>

    <div class="form-group">
        <label><?php echo __('audience'); ?></label>
        <div class="multi-select-group">
            <?php foreach ($data['audiences'] as $audience): ?>
                <label><input type="checkbox" name="audiences[]" value="<?php echo $audience->id; ?>" <?php echo in_array($audience->id, $data['selected_audiences'] ?? []) ? 'checked' : ''; ?>> <?php echo htmlspecialchars($audience->name); ?></label>
            <?php endforeach; ?>
        </div>
        <?php if (isset($data['errors']['audiences'])): ?><span class="error-text"><?php echo $data['errors']['audiences']; ?></span><?php endif; ?>
    </div>

    <div class="form-group">
        <label><?php echo __('organizer'); ?></label>
        <div class="multi-select-group">
            <?php foreach ($data['organizers'] as $organizer): ?>
                <label><input type="checkbox" name="organizers[]" value="<?php echo $organizer->id; ?>" <?php echo in_array($organizer->id, $data['selected_organizers'] ?? []) ? 'checked' : ''; ?>> <?php echo htmlspecialchars($organizer->name); ?></label>
            <?php endforeach; ?>
        </div>
        <?php if (isset($data['errors']['organizers'])): ?><span class="error-text"><?php echo $data['errors']['organizers']; ?></span><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="objective"><?php echo __('objective'); ?></label>
        <textarea id="objective" name="objective" required><?php echo htmlspecialchars($data['objective'] ?? ''); ?></textarea>
        <?php if (isset($data['errors']['objective'])): ?><span class="error-text"><?php echo $data['errors']['objective']; ?></span><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="priority"><?php echo __('priority'); ?></label>
        <select id="priority" name="priority">
            <option value="low"><?php echo __('low'); ?></option>
            <option value="medium" selected><?php echo __('medium'); ?></option>
            <option value="high"><?php echo __('high'); ?></option>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?php echo __('submit'); ?></button>
    </div>
</form>
