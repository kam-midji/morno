<div class="header">
    <h1>داشبورد ادمین</h1>
    <a href="index.php?url=users/logout" class="btn btn-secondary"><?php echo __('logout'); ?></a>
</div>

<p>به پنل مدیریت خوش آمدید. از اینجا می‌توانید بخش‌های مختلف سیستم را مدیریت کنید.</p>

<style>
    .admin-menu {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }
    .admin-menu-item {
        display: block;
        padding: 20px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        color: #343a40;
        font-size: 1.2em;
        font-weight: bold;
        transition: all 0.2s ease-in-out;
    }
    .admin-menu-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background-color: #e9ecef;
    }
</style>

<div class="admin-menu">
    <a href="index.php?url=admin/users" class="admin-menu-item">مدیریت کاربران</a>
    <a href="index.php?url=admin/semesters" class="admin-menu-item">مدیریت ترم‌ها</a>
    <a href="index.php?url=admin/audiences" class="admin-menu-item">مدیریت مخاطبان</a>
    <a href="index.php?url=admin/organizers" class="admin-menu-item">مدیریت برگزارکنندگان</a>
</div>
