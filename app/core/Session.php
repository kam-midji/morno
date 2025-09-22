<?php

// Session Management Helper
class Session {

    /**
     * Starts the session if not already started.
     */
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Sets a value in the session.
     * @param string $key The key.
     * @param mixed $value The value.
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Gets a value from the session.
     * @param string $key The key.
     * @return mixed The value, or null if the key does not exist.
     */
    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Checks if a key exists in the session.
     * @param string $key The key.
     * @return bool True if the key exists, false otherwise.
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    /**
     * Removes a key from the session.
     * @param string $key The key to remove.
     */
    public static function remove($key) {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroys the entire session.
     */
    public static function destroy() {
        session_destroy();
    }

    /**
     * Sets a flash message that will be available on the next request only.
     * @param string $key The key for the flash message.
     * @param string $message The message.
     */
    public static function flash($key, $message) {
        self::set('flash_' . $key, $message);
    }

    /**
     * Gets a flash message and then removes it.
     * @param string $key The key for the flash message.
     * @return string|null The message, or null if it doesn't exist.
     */
    public static function getFlash($key) {
        $message = self::get('flash_' . $key);
        if ($message) {
            self::remove('flash_' . $key);
        }
        return $message;
    }

    /**
     * Checks for a "Remember Me" cookie and logs the user in if valid.
     * This should be called early in the application bootstrap process.
     */
    public static function checkRememberMe() {
        // If user is already logged in, do nothing.
        if (self::has('user_id')) {
            return;
        }

        if (isset($_COOKIE['remember_me'])) {
            @list($selector, $validator) = explode(':', $_COOKIE['remember_me']);

            if (empty($selector) || empty($validator)) {
                // Invalid cookie format, clear it
                setcookie('remember_me', '', time() - 3600, '/');
                return;
            }

            // We need the User model to check the token.
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User();

            $token = $userModel->findTokenBySelector($selector);

            if ($token && password_verify($validator, $token->hashed_validator)) {
                // Token is valid. Log the user in.
                $user = $userModel->findById($token->user_id);

                if ($user) {
                    // Set session variables
                    self::set('user_id', $user->id);
                    self::set('username', $user->username);
                    self::set('user_role', $user->role);

                    // --- Token Rotation ---
                    $newValidator = bin2hex(random_bytes(32));
                    $newHashedValidator = password_hash($newValidator, PASSWORD_DEFAULT);
                    $expiresAt = date('Y-m-d H:i:s', time() + (86400 * 30)); // 30-day expiry

                    $userModel->updateToken($token->id, $selector, $newHashedValidator, $expiresAt);

                    $cookieValue = $selector . ':' . $newValidator;
                    setcookie('remember_me', $cookieValue, time() + (86400 * 30), '/', '', false, true);

                } else {
                    // User not found, clear the invalid cookie
                     setcookie('remember_me', '', time() - 3600, '/');
                }
            } elseif ($token) {
                // Selector was found, but validator was wrong (theft attempt?).
                // Invalidate all tokens for this user for security.
                $userModel->deleteToken($token->user_id);
                setcookie('remember_me', '', time() - 3600, '/');
            }
        }
    }
}
