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
}
