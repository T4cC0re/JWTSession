<?php
/**
 * Copyright 2017 Hendrik "T4cC0re" Meyer / github.com/T4cC0re
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * This code is originated fully in my spare time and does not represent work
 * for my Employer at the time of creation!
 */

include "php-jwt/JWT.php";

/**
 * Class JWTSession
 *
 * A PHP 5.2 compatible "session store" using JWTs in a cookie
 *
 * All code required is:
 *    JWTSession::setKey("ASDF");
 *    JWTSession::load();
 *    register_shutdown_function('JWTSession::save');
 *
 * It does not implement the 'official' session interface.
 *
 * @uses firebase/php-jwt
 */
class JWTSession
{
    /**
     * Allowed time-drift in seconds
     *
     * @var int
     */
    private static $_leeway = 60;

    /**
     * Token validity in seconds
     *
     * @var int
     */
    private static $_validity = 1800;

    /**
     * The secret to be used.
     *
     * @see setKey
     *
     * @var string
     */
    private static $_HMACSecret;

    /**
     * @var array|null
     */
    private static $_whitelist = null;

    /**
     * Set the key used for creation and validation of the tokens
     *
     * @param $HMACSecret
     *
     * @return boolean
     */
    public static function setKey($HMACSecret) {
        self::$_HMACSecret = $HMACSecret;

        return (self::$_HMACSecret === $HMACSecret);
    }

    public static function setWhitelist(array $whitelist) {
        self::$_whitelist = $whitelist;

        return (self::$_whitelist === $whitelist);
    }

    /**
     * Load a token, validate and apply content to $_SESSION.
     */
    public static function load() {
        JWT::$leeway = self::$_leeway;

        if(false === isset($_SESSION)){
            $_SESSION = array();
        }

        if (false === isset($_COOKIE['JWTSession'])) {
            return null;
        }


        try {
            $content = get_object_vars(
                JWT::decode(
                    $_COOKIE['JWTSession'],
                    self::$_HMACSecret,
                    array('HS256')
                )
            );
        } catch (Exception $exception) {
            // Validation has gone wrong. oops!
            error_log('JWT::decode: ' . $exception->getMessage());

            return null;
        }

        if ($content['aud'] !== $_SERVER['SERVER_NAME']) {
            error_log('JWTSession::load: aud claim mismatch');

            return null;
        }

        $_SESSION = $content;
    }

    /**
     * Clears $_SESSION and invalidates the cookie.
     */
    public static function clear() {
        $_SESSION = array();

        setcookie("JWTSession", '', 1, '/');
    }

    /**
     * Saves the content of $_SESSION and sets a cookie with the token.
     */
    public static function save() {
        if (self::$_whitelist === null) {
            $input = $_SESSION;
        } else {
            $input = array_intersect_key($_SESSION, array_flip(self::$_whitelist));
        }

        // var_dump($input);

        $input['aud'] = $_SERVER['SERVER_NAME'];
        $input['iat'] = time();
        $input['exp'] = time() + self::$_validity;

        try {
            $token = JWT::encode($input, self::$_HMACSecret, 'HS256');
        } catch (Exception $exception) {
            // Validation has gone wrong. oops!
            error_log('JWT::encode: ' . $exception->getMessage());

            return false;
        }

        return setcookie("JWTSession", $token, time() + self::$_validity, '/');
    }
}
