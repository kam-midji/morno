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

        // Initialize date-only picker
        jalaliDatepicker.startWatch({
            selector: '[data-jdp-date-only]',
            time: false,
            persianDigits: true,
            format: 'YYYY/MM/DD'
        });

        // Modal Logic
        const modal = document.getElementById('quick-add-modal');
        const fab = document.getElementById('fab-add-proposal');
        const closeModalBtn = document.querySelector('.modal-close-btn');

        if(modal && fab && closeModalBtn) {
            fab.addEventListener('click', () => {
                modal.classList.add('active');
            });

            const closeModal = () => {
                modal.classList.remove('active');
            };

            closeModalBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }

        // Profile Menu Dropdown Logic
        const profileMenuBtn = document.getElementById('profile-menu-btn');
        const profileDropdown = document.getElementById('profile-menu-dropdown');

        if (profileMenuBtn && profileDropdown) {
            profileMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            window.addEventListener('click', (e) => {
                if (!profileDropdown.contains(e.target) && !profileMenuBtn.contains(e.target)) {
                    profileDropdown.classList.remove('active');
                }
            });
        }
    </script>

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
                    <input type="text" id="modal-event-datetime" name="event_datetime" data-jdp required>
                </div>
                <div class="form-group">
                    <label for="modal-event-end-datetime">زمان پایان</label>
                    <input type="text" id="modal-event-end-datetime" name="event_end_datetime" data-jdp>
                </div>
                 <div class="form-group">
                    <label for="modal-objective">هدف برنامه</label>
                    <textarea id="modal-objective" name="objective" required></textarea>
                </div>
                <div class="form-group">
                    <label>مخاطبان</label>
                    <div class="multi-select-group modal-multi-select">
                        <?php $audiences = Session::get('modal_audiences', []); ?>
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
                        <?php $organizers = Session::get('modal_organizers', []); ?>
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
                <input type="hidden" name="current_semester_id" value="<?php echo htmlspecialchars(Session::get('current_semester_id') ?? ''); ?>">
                <button type="submit" class="btn btn-primary">ثبت</button>
            </form>
        </div>
    </div>
</body>
</html>