<div class="container">
    <div class="login-container">
        <h1><?php echo __('login'); ?></h1>

        <?php /* Placeholder for flash messages, e.g.
        if (Session::has('flash_error')) {
            echo '<div class="flash-message flash-error">' . Session::getFlash('error') . '</div>';
        }
        */ ?>

        <form action="index.php?url=users/login" method="POST">
            <div class="form-group">
                <label for="username"><?php echo __('username'); ?></label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>
                <?php if (!empty($data['username_err'])): ?><span class="error-text"><?php echo $data['username_err']; ?></span><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password"><?php echo __('password'); ?></label>
                <input type="password" id="password" name="password" required>
                <?php if (!empty($data['password_err'])): ?><span class="error-text"><?php echo $data['password_err']; ?></span><?php endif; ?>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="remember_me" name="remember_me">
                <label for="remember_me"><?php echo __('remember_me'); ?></label>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo __('login'); ?></button>
        </form>
    </div>
</div>