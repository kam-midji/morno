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

</body>
</html>
