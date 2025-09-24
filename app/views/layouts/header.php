<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['page_title'] ?? SITENAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/jalalidatepicker.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>
<body>
    <header class="site-header">
        <div class="header-left">
            <?php if (isset($data['show_back_button']) && $data['show_back_button']): ?>
                <a href="<?php echo $data['back_button_url'] ?? 'javascript:history.back()'; ?>" class="header-icon-btn"><i class="fas fa-arrow-right"></i></a>
            <?php endif; ?>
        </div>
        <div class="header-center">
            <h1 class="page-title"><?php echo $data['page_title'] ?? __('appName'); ?></h1>
        </div>
        <div class="header-right">
            <div class="profile-menu">
                <button id="profile-menu-btn" class="header-icon-btn profile-icon-btn">
                    <span><?php echo strtoupper(substr(Session::get('username') ?? 'U', 0, 1)); ?></span>
                </button>
                <div id="profile-menu-dropdown" class="profile-dropdown">
                    <a href="index.php?url=users/logout"><?php echo __('logout'); ?></a>
                </div>
            </div>
        </div>
    </header>
    <div class="container">
