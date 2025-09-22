</div> <!-- /container -->

    <!-- Jalali Date Picker JS -->
    <script type="text/javascript" src="assets/js/jalalidatepicker.min.js"></script>
    <script type="text/javascript">
        // Initialize date picker on any input with data-jdp attribute
        jalaliDatepicker.startWatch({
            time: true,
            persianDigits: true,
            format: 'YYYY/MM/DD HH:mm:ss'
        });

        // Initialize time-only picker
        jalaliDatepicker.startWatch({
            selector: '[data-jdp-time-only]',
            time: true,
            date: false,
            persianDigits: true,
            format: 'HH:mm'
        });

        // View Toggler Logic
        const gridView = document.querySelector('.calendar-grid-container');
        const agendaView = document.querySelector('.agenda-view');
        const gridBtn = document.getElementById('show-grid-btn');
        const agendaBtn = document.getElementById('show-agenda-btn');

        if (gridBtn && agendaBtn) {
            gridBtn.addEventListener('click', () => {
                gridView.style.display = 'grid';
                agendaView.style.display = 'none';
                gridBtn.classList.add('btn-primary');
                gridBtn.classList.remove('btn-secondary');
                agendaBtn.classList.add('btn-secondary');
                agendaBtn.classList.remove('btn-primary');
            });

            agendaBtn.addEventListener('click', () => {
                gridView.style.display = 'none';
                agendaView.style.display = 'block';
                agendaBtn.classList.add('btn-primary');
                agendaBtn.classList.remove('btn-secondary');
                gridBtn.classList.add('btn-secondary');
                gridBtn.classList.remove('btn-primary');
            });
        }

        // Modal Logic
        const modal = document.getElementById('quick-add-modal');
        const fab = document.getElementById('fab-add-proposal');
        const closeModalBtn = document.querySelector('.modal-close-btn');

        if(modal && fab && closeModalBtn) {
            fab.addEventListener('click', () => {
                modal.style.display = 'flex';
            });

            closeModalBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Auto-set end time based on start time
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        if(startTimeInput && endTimeInput && startDateInput && endDateInput) {
            startTimeInput.addEventListener('change', () => {
                const startTime = startTimeInput.value;
                if(startTime) {
                    const [hours, minutes] = startTime.split(':');
                    const startDate = new Date(); // Dummy date object
                    startDate.setHours(parseInt(hours));
                    startDate.setMinutes(parseInt(minutes));
                    startDate.setHours(startDate.getHours() + 1); // Add one hour

                    const endHours = String(startDate.getHours()).padStart(2, '0');
                    const endMinutes = String(startDate.getMinutes()).padStart(2, '0');

                    endTimeInput.value = `${endHours}:${endMinutes}`;
                    // Also set the end date to be the same as the start date
                    if(startDateInput.value) {
                        endDateInput.value = startDateInput.value;
                    }
                }
            });
        }
    </script>

    <!-- Quick Add Modal -->
    <div id="quick-add-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <span class="modal-close-btn">&times;</span>
            <h3>ایجاد پیشنهاد سریع</h3>
            <form id="quick-add-form" action="index.php?url=proposals/add" method="POST">
                <!-- This form will be simpler, more can be added -->
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
                    <label for="modal-audiences">مخاطبان</label>
                    <select id="modal-audiences" name="audiences[]" multiple required>
                        <?php if (!empty($data['audiences'])): ?>
                            <?php foreach ($data['audiences'] as $audience): ?>
                                <option value="<?php echo $audience->id; ?>"><?php echo htmlspecialchars($audience->name); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal-organizers">برگزارکنندگان</label>
                    <select id="modal-organizers" name="organizers[]" multiple required>
                         <?php if (!empty($data['organizers'])): ?>
                            <?php foreach ($data['organizers'] as $organizer): ?>
                                <option value="<?php echo $organizer->id; ?>"><?php echo htmlspecialchars($organizer->name); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal-priority">اولویت</label>
                    <select id="modal-priority" name="priority" required>
                        <option value="medium" selected>متوسط</option>
                        <option value="high">زیاد</option>
                        <option value="low">کم</option>
                    </select>
                </div>
                <input type="hidden" name="current_semester_id" value="<?php echo htmlspecialchars($data['current_semester']->id ?? ''); ?>">
                <button type="submit" class="btn btn-primary">ثبت</button>
            </form>
        </div>
    </div>
</body>
</html>
