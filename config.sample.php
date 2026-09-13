<?php
/*
 * Copy this file to config.php and fill it in. config.php is the only file
 * that ever sees your Cheddar Up key -- .htaccess blocks it from being served,
 * and it is listed in .gitignore so it never lands in a repository.
 */

return array(

  // Your Cheddar Up API key, exactly as the account settings page shows it.
  'api_key' => 'PASTE-YOUR-CHEDDAR-UP-API-KEY-HERE',

  // How the key is presented to Cheddar Up. If you do not know, leave this on
  // 'bearer' and try the others from the page's Connection panel until one
  // stops returning 401. In order of how common they are:
  //   'bearer'  ->  Authorization: Bearer <key>
  //   'token'   ->  Authorization: Token <key>
  //   'x-api-key'  ->  X-Api-Key: <key>
  //   'basic'   ->  HTTP basic auth, key as the username, blank password
  //   'query'   ->  ?api_key=<key> appended to the URL
  'auth_style' => 'bearer',

  // Base URL every request is joined onto. Only hosts listed here can be
  // reached, so this doubles as the guard against the proxy being pointed at
  // something else.
  'base_url' => 'https://api.cheddarup.com',

  // A password of your own choosing, shared with the page. Leave it empty to
  // turn the check off, but do not: without it, anyone who finds the URL of
  // cheddar.php can read your Cheddar Up account through it. Any long random
  // string will do.
  'access_token' => '',

  // Seconds to wait on Cheddar Up before giving up.
  'timeout' => 20,
);
