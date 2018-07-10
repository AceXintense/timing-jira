<?php

namespace Core;

/*
 * Authentication class to handle all the Authentication for JIRA.
 */
class Authentication
{
    private static $username;
    private static $password;

    /**
     * Returns a Base64 string of your username:password.
     * @return string
     */
    public static function getBasicAuthentication()
    {
        return base64_encode(self::$username . ':' . self::$password);
    }

    /**
     * Sets the username in memory.
     * @param string $username
     */
    public static function setUsername($username)
    {
        self::$username = $username;
    }

    /**
     * Sets the password in memory.
     * @param string $password
     */
    public static function setPassword($password)
    {
        self::$password = $password;
    }

    /**
     * Sets the username and password in memory.
     * @param string $username
     * @param string $password
     * @param bool $base64encoded
     */
    public static function setUsernameAndPassword($username, $password, $base64encoded = false)
    {
        self::$username = $username;
        if ($base64encoded) {
            self::$password = base64_decode($password);
        } else {
            self::$password = $password;
        }
    }

    /**
     * Returns the username.
     * @return string
     */
    public static function getUsername()
    {
        return self::$username;
    }

    /**
     * Gets the credentials from a file.
     * @param $path
     */
    public static function setAuthenticationFromFile($path)
    {
        $credentials = json_decode(file_get_contents($path), true);
        self::setUsernameAndPassword($credentials['username'], $credentials['password']);
    }
}