# JWTSession

### A PHP 5.2 compatible "session store" using JWTs in a cookie

### Quick launch:

Paste the following code in the beginning of your entrypoint:

```php
include "<myDeps>/JWTSession.php";
JWTSession::setKey("ASDF");                     // The key to sign and verify the JWTs
JWTSession::load();                             // Load $_SESSION from JWT stored in the cookie
register_shutdown_function('JWTSession::save'); // Make sure the cookie gets saved.
```

### `JWTSession::setWhitelist(<array>);`

Call this function to limit the exported keys.  
e.g.:

```php
JWTSession::setWhitelist(array('asdf')); // Whitelist $_SESSION['asdf'] for export
```

Will only have `$_SESSION['asdf']` appear in your token. Everything else will be ignored.

###`JWTSession::save();`

Call this to explicitly save the cookie. If you registered a shutdown function you don't need to call this unless you print something on the page (thus send headers before page shutdown). In such cases call `JWTSession::save();` to make sure the session has been saved in a cookie.

## General

 - Tokens are signed with HMAC256
 - You control the secret.
 - The token is only valid for the same `$_SERVER['SERVER_NAME']`
 - Use the whitelist feature to exclude any legacy `$_SESSION` values you don't need.
 - Do not expose sensitive data via JWT. If you need to do this, this is not the correct choice for you!
 - If you set a whitelist, only those values will be allowed.
 - If you DO NOT set a whitelist, ALL values will be exported.

## License

  Licensed under the Apache-2.0 License (see LICENSE)
  
  Uses a modified version of [firebase/php-jwt][1] licensed under the BSD-3-Clause License (see php-jwt/LICENSE) 

#### Disclaimer

###### This code is originated fully in my spare time and does not represent work for my Employer at the time of creation!

[1]: https://github.com/firebase/php-jwt
