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

        // View Toggler Logic
        const gridView = document.querySelector('.weekly-grid');
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
                <!-- Hidden fields for audiences, organizers etc. would be needed for full validation -->
                <!-- Or a simplified submission that requires editing later for full details -->
                <button type="submit" class="btn btn-primary">ثبت</button>
            </form>
        </div>
    </div>

    <!-- Floating Action Button -->
    <button id="fab-add-proposal" class="fab">+</button>
</body>
</html>
