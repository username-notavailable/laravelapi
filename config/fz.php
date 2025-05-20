<?php

return [
    'commons' => [
        'forceHTTPS' => env('FZ_FORCE_HTTPS', false)
    ],
    'load' =>[
        'functions' => [
            'blade' => env('FZ_LOAD_FUNCTIONS_BLADE', false),
            'helpers' => env('FZ_LOAD_FUNCTIONS_HELPERS', false)
        ],
        'cookies' => [
            'locale' => env('FZ_LOAD_COOKIES_LOCALE', false),
            'theme' => env('FZ_LOAD_COOKIES_THEME', false),
        ],
        'routes' => env('FZ_LOAD_ROUTES', false)
    ],
    'ui' => [
        'themes' => [
            'default'
        ]
    ],
    'keycloak' => [
        'login' => [
            'type' => env('FZ_KC_LOGIN_TYPE', 'Frontend'), // Frontend, Backend
            'loginViewName' => env('FZ_KC_LOGIN_LOGIN_VIEW_NAME', 'login'),
            'issuesViewName' => env('FZ_KC_LOGIN_ISSUES_VIEW_NAME', 'errors.generic'),
            'postRouteName' => env('FZ_KC_LOGIN_POST_ROUTE_NAME', 'index'),
            
        ],
        'logout' => [
            'allowClientIdxFromRequest' => env('FZ_KC_LOGOUT_ALLOW_CLIENTIDX_FROM_REQUEST', false),
            'skipConfirmation' => env('FZ_KC_LOGOUT_SKIP_CONFIRMATION', false),
            'postRouteName' => env('FZ_KC_LOGOUT_POST_ROUTE_NAME', 'index'),
        ],
        'client' => [
            'idxs' => [
                'default' => [
                    'loginHostname' => env('FZ_KC_LOGIN_HOSTNAME', ''),
                    'authType' => env('FZ_KC_AUTH_TYPE', 'ClientSecret'), // ClientSecret, SignedJwt, SignedJwtClientSecret
                    'realmName' => env('FZ_KC_REALM_NAME', ''),
                    'clientId' => env('FZ_KC_CLIENT_ID', ''),
                    'clientSecret' => env('FZ_KC_CLIENT_SECRET', ''),
                    'authPayload' => [],
                    'authAlg' => env('FZ_KC_AUTH_ALG', ''),
                    'pubKeyName' => env('FZ_KC_PUB_KEY_NAME', ''),
                    'pvtKeyName' => env('FZ_KC_PVT_KEY_NAME', ''),
                    'certName' => env('FZ_KC_CERT_NAME', '')
                ]
            ],
            'cache' => [
                // host: string. can be a host, or the path to a unix domain socket.
                // port: int (default is 6379, should be -1 for unix domain socket)
                // connectTimeout: float, value in seconds (default is 0 meaning unlimited)
                // retryInterval: int, value in milliseconds (optional)
                // readTimeout: float, value in seconds (default is 0 meaning unlimited)
                // persistent: mixed, if value is string then it used as persistent id, else value casts to boolean
                // auth: mixed, authentication information 
                // database: int, database number 
                // ssl: array, SSL context options
                'redis' => [ //phpredis
                    'prefix' => env('FZ_CLIENT_REDIS_PREFIX', ''),
                    'init' => [
                        'host' => env('FZ_CLIENT_REDIS_HOST', '127.0.0.1'),
                        'port' => (int)env('FZ_CLIENT_REDIS_PORT', 6379),
                        'connectTimeout' => (float)env('FZ_CLIENT_REDIS_TIMEOUT', 2.5),
                        'retryInterval' => (int)env('FZ_CLIENT_REDIS_RETRY_INTERVAL', null),
                        'readTimeout' => (float)env('FZ_CLIENT_REDIS_READ_TIMEOUT', 0),
                        'persistent' => env('FZ_CLIENT_REDIS_PERSISTENT_ID', 'kc-client-cache'),
                        'auth' => [env('FZ_CLIENT_REDIS_USERNAME', ''), env('FZ_CLIENT_REDIS_PASSWORD', '')],
                        //'database' => (int)env('FZ_CLIENT_REDIS_DB', 2), // ??? D.M.
                        //'ssl' => ['verify_peer' => (bool)env('FZ_CLIENT_REDIS_SSL_VERIFY_PEER', false)],
                    ],
                    'options' => []
                ],
                //
                'memcached' => [ //memcached
                    //'prefix' => env('FZ_CLIENT_MEMCACHED_PREFIX', ''),
                    'init' => [
                        'persistent' => env('FZ_CLIENT_MEMCACHED_PERSISTENT_ID', 'kc-client-cache'),
                        'auth' => [env('FZ_CLIENT_MEMCACHED_USERNAME', ''), env('FZ_CLIENT_MEMCACHED_PASSWORD', '')],
                        //'database' => (int)env('FZ_CLIENT_MEMCACHED_DB', 2), // ??? D.M.
                        //'ssl' => ['verify_peer' => false],
                    ],
                    'options' => [
                        // some nicer default options
                        'OPT_BINARY_PROTOCOL' => (bool)env('FZ_CLIENT_MEMCACHED_OPT_BINARY_PROTOCOL', true),
                        // - nicer TCP options
                        'OPT_TCP_NODELAY' => (bool)env('FZ_CLIENT_MEMCACHED_OPT_TCP_NODELAY', true),
                        'OPT_NO_BLOCK' => (bool)env('FZ_CLIENT_MEMCACHED_OPT_NO_BLOCK', true),
                        // // - timeouts
                        'OPT_CONNECT_TIMEOUT' => (int)env('FZ_CLIENT_MEMCACHED_OPT_CONNECT_TIMEOUT', 2000), // ms
                        'OPT_POLL_TIMEOUT' => (int)env('FZ_CLIENT_MEMCACHED_OPT_POLL_TIMEOUT', 2000),       // ms
                        'OPT_RECV_TIMEOUT' => (int)env('FZ_CLIENT_MEMCACHED_OPT_RECV_TIMEOUT', 750 * 1000), // us
                        'OPT_SEND_TIMEOUT' => (int)env('FZ_CLIENT_MEMCACHED_OPT_SEND_TIMEOUT', 750 * 1000), // us
                        // - better failover
                        'OPT_DISTRIBUTION' => (int)env('FZ_CLIENT_MEMCACHED_OPT_DISTRIBUTION', 1), // Memcached::DISTRIBUTION_CONSISTENT
                        'OPT_LIBKETAMA_COMPATIBLE' => (bool)env('FZ_CLIENT_MEMCACHED_OPT_LIBKETAMA_COMPATIBLE', true),
                        'OPT_RETRY_TIMEOUT' => (int)env('FZ_CLIENT_MEMCACHED_OPT_RETRY_TIMEOUT', 2),
                        'OPT_SERVER_FAILURE_LIMIT' => (int)env('FZ_CLIENT_MEMCACHED_OPT_SERVER_FAILURE_LIMIT', 5),
                        'OPT_SERVER_TIMEOUT_LIMIT' => (int)env('FZ_CLIENT_MEMCACHED_OPT_SERVER_TIMEOUT_LIMIT', 0),
                        'OPT_AUTO_EJECT_HOSTS' => (bool)env('FZ_CLIENT_MEMCACHED_OPT_AUTO_EJECT_HOSTS', true),
                    ],
                    'servers' => [
                        [env('FZ_CLIENT_MEMCACHED_HOST', '127.0.0.1'), env('FZ_CLIENT_MEMCACHED_PORT', 11211), env('FZ_CLIENT_MEMCACHED_WEIGHT', 100)]
                    ]
                ]
            ]
        ],
    ],
    'i18n' => [
        'locales' => [
            'it_IT' => [
                'name' => 'Italiano',
                'code' => 'it_IT',
                'flagIco' => 'ita'
            ],
            'en_US' => [
                'name' => 'English',
                'code' => 'en_US',
                'flagIco' => 'usa'
            ],
            'de_DE' => [
                'name' => 'Deutsch',
                'code' => 'de_DE',
                'flagIco' => 'deu'
            ],
            'es_ES' => [
                'name' => 'Español',
                'code' => 'es_ES',
                'flagIco' => 'esp'
            ],
            'fr_FR' => [
                'name' => 'Français',
                'code' => 'fr_FR',
                'flagIco' => 'fra'
            ],
            'ru_RU' => [
                'name' => 'Русский',
                'code' => 'ru_RU',
                'flagIco' => 'rus'
            ],
            'pt_PT' => [
                'name' => 'Português',
                'code' => 'pt_PT',
                'flagIco' => 'prt'
            ]
        ],
    ],
    'log' => [
        'sql' => env('FZ_LOG_SQL', false),
        'login' => [
            'success' => [
                'request' => env('FZ_LOG_LOGIN_SUCCESS_REQUEST', false),
                'level' => env('FZ_LOG_LOGIN_SUCCESS_LEVEL', 'debug'),
            ],
            'fail' => [
                'request' => env('FZ_LOG_LOGIN_FAIL_REQUEST', false),
                'level' => env('FZ_LOG_LOGIN_FAIL_LEVEL', 'debug'),
            ],
            'lockout' => [
                'request' => env('FZ_LOG_LOGIN_LOCKOUT_REQUEST', false),
                'level' => env('FZ_LOG_LOGIN_LOCKOUT_LEVEL', 'debug'),
            ],
            'maxAttempts' => env('FZ_LOG_LOGIN_MAX_ATTEMPTS', 5),
            'decaySeconds' => env('FZ_LOG_LOGIN_DECAY_SECONDS', 50)
        ]
    ],
    'default' => [
        'locale' => env('FZ_DEFAULT_LOCALE', 'en_US'),
        'localeCookieName' => env('FZ_DEFAULT_LOCALE_COOKIE_NAME', '__fz_lcl__'),
        'themeCookieName' => env('FZ_DEFAULT_THEME_COOKIE_NAME', '__fz_thm__'),
        'ui' => [
            'theme' => env('FZ_DEFAULT_UI_THEME', 'default')
        ],
        'keycloak' => [
            'authGuardName' => 'keyCloak',
            'clientIdx' => env('FZ_DEFAULT_KEYCLOAK_CLIENT_IDX', 'default'),
            'clientCacheType' => env('FZ_DEFAULT_KEYCLOAK_CLIENT_CACHE_TYPE', 'memcached'),
            'onlyGuestFailRouteName' => env('FZ_DEFAULT_KEYCLOAK_ONLY_GUEST_FAIL_ROUTE_NAME', 'index')
        ],
        'sweetapi' => [
            'info' => [
                'title' => env('FZ_DEFAULT_SWEETAPI_TITLE', 'SweetAPI'),
                'description' => env('FZ_DEFAULT_SWEETAPI_DESCRIPTION', ''),
                'termsOfService' => env('FZ_DEFAULT_SWEETAPI_TERMS_OF_SERVICE', ''),
                'contact' => [
                    'name' => env('FZ_DEFAULT_SWEETAPI_CONTACT_NAME', ''),
                    'url' => env('FZ_DEFAULT_SWEETAPI_CONTACT_URL', ''),
                    'email' => env('FZ_DEFAULT_SWEETAPI_CONTACT_EMAIL', '')
                ],
                'license' => [
                    'name' => env('FZ_DEFAULT_SWEETAPI_LICENSE_NAME', ''),
                    'url' => env('FZ_DEFAULT_SWEETAPI_LICENSE_URL', '')
                ],
                'version' => env('FZ_DEFAULT_SWEETAPI_OPEN_API_DOC_VERSION', '1.0.0'),
                'externalDocs' => [
                    'url' => env('FZ_DEFAULT_SWEETAPI_EXT_DOC_URL', ''),
                    'description' => env('FZ_DEFAULT_SWEETAPI_EXT_DESCRIPTION', '')
                ]
            ]
        ]
    ]
];