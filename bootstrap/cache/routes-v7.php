<?php

/*
|--------------------------------------------------------------------------
| Load The Cached Routes
|--------------------------------------------------------------------------
|
| Here we will decode and unserialize the RouteCollection instance that
| holds all of the route information for an application. This allows
| us to instantaneously load the entire route map into the router.
|
*/

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/sanctum/csrf-cookie' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'sanctum.csrf-cookie',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/auth/register' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::auVwvoPfl1bFQMCH',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/auth/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::AVOi7TXOJXU0xMd5',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/auth/forgot-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dZNF1ZHBaZfLHKpa',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/auth/me' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5kW70azR1xknHoTU',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/auth/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Mq1na36YcBguYBCH',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/auth/refresh' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NL2Bg8II5Rk4mWTL',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FNiTR6tIPjNsmuSy',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::q6i6nf3PD1Se7hLr',
          ),
          1 => NULL,
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/profile/change-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::baMBjqOWaGLP6hTZ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2bbrvcQ7ZM1PqSyJ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/products' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8YiUSl6ShOMnpUOs',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jQR1kSehmkU8cxI4',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/products/search' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dOEBgkjoNtDCbMtj',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/pantries' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::T31foYwdcZsIIBRH',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::14pxibcz1ZVqfYnW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/v1/shopping-lists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ICsffPoErj5V1u4U',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hjUUmiDrXmswDl5M',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/test' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vBFdTIIiGJxDx28L',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::BorMqwWktXMe80rB',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/api/v1/(?|p(?|roducts/(?|barcode/([^/]++)(?|(*:52)|/(?|nutritional(*:74)|suggestions(*:92)))|([^/]++)(?|(*:112)))|antr(?|ies/(?|([^/]++)(?|(*:147)|/(?|items(?|(*:167)|/([^/]++)(?|(*:187)))|notifications(*:210)|share(*:223)))|shared/([^/]++)(*:248))|y/shared/([^/]++)(*:274)))|shopping\\-list(?|s/(?|([^/]++)(?|(*:317)|/(?|items(?|(*:337)|/([^/]++)(?|/(?|purchased(*:370)|unpurchased(*:389))|(*:398)))|complete(*:416)|move\\-to\\-pantry(*:440)|s(?|uggestions(*:462)|hare(*:474))))|shared/([^/]++)(*:500))|/shared/([^/]++)(*:525))))/?$}sDu',
    ),
    3 => 
    array (
      52 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YS66yz2vMYIOUv0a',
          ),
          1 => 
          array (
            0 => 'barcode',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      74 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EWyGcgl0gyC4FGq7',
          ),
          1 => 
          array (
            0 => 'barcode',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      92 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vERsC0IZdv4213dz',
          ),
          1 => 
          array (
            0 => 'barcode',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      112 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::JGtzsaYroSBHCF7H',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dUNYsIBtk55lCSoN',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vUdoOr1v3WZd2AuO',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      147 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Eii3oQOprTBkJXBx',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::iWT02jMeHoJ4M0gg',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nZC4A8PyztcEDP5E',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      167 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::K0VXxICgaVZ847Od',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      187 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LTjrJ3qtURk0YfMH',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'itemId',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::l7GUb95trfW9wr3f',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'itemId',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      210 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Xz4O9kCDntnurwqm',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      223 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::W2GNfoLN95AZqzUB',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      248 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ecpkLiaHIYIiNfnL',
          ),
          1 => 
          array (
            0 => 'token',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      274 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4QKtk80Go4vsWk3T',
          ),
          1 => 
          array (
            0 => 'token',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      317 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::iyW8DnfqJLG0YBAk',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::3eZ4UqXma6N4Tlz9',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GcapKbmTVyDrvQEK',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      337 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::74UtIUXE7tcdXjEO',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      370 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::F8OuiaEtevckhvKo',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'itemId',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      389 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FDyBzR6LUdreVowO',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'itemId',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      398 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5ZnNu4YLyUel4k56',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'itemId',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      416 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::68dV1ooMmIsCLDwJ',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      440 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kppghxDbMawyizgR',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      462 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dvESHhYcFSrPMYS9',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      474 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::zO1rsbMkwbgoKxnw',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      500 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rqVLd6Rzead4fc0h',
          ),
          1 => 
          array (
            0 => 'token',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      525 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xKWHBfcWyqfcqDl2',
          ),
          1 => 
          array (
            0 => 'token',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'sanctum.csrf-cookie' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'sanctum/csrf-cookie',
      'action' => 
      array (
        'uses' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'controller' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'namespace' => NULL,
        'prefix' => 'sanctum',
        'where' => 
        array (
        ),
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'sanctum.csrf-cookie',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::auVwvoPfl1bFQMCH' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/auth/register',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AuthController@register',
        'controller' => 'App\\Http\\Controllers\\Auth\\AuthController@register',
        'namespace' => NULL,
        'prefix' => 'api/v1/auth',
        'where' => 
        array (
        ),
        'as' => 'generated::auVwvoPfl1bFQMCH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::AVOi7TXOJXU0xMd5' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/auth/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AuthController@login',
        'controller' => 'App\\Http\\Controllers\\Auth\\AuthController@login',
        'namespace' => NULL,
        'prefix' => 'api/v1/auth',
        'where' => 
        array (
        ),
        'as' => 'generated::AVOi7TXOJXU0xMd5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dZNF1ZHBaZfLHKpa' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/auth/forgot-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AuthController@forgotPassword',
        'controller' => 'App\\Http\\Controllers\\Auth\\AuthController@forgotPassword',
        'namespace' => NULL,
        'prefix' => 'api/v1/auth',
        'where' => 
        array (
        ),
        'as' => 'generated::dZNF1ZHBaZfLHKpa',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5kW70azR1xknHoTU' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/auth/me',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AuthController@me',
        'controller' => 'App\\Http\\Controllers\\Auth\\AuthController@me',
        'namespace' => NULL,
        'prefix' => 'api/v1/auth',
        'where' => 
        array (
        ),
        'as' => 'generated::5kW70azR1xknHoTU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Mq1na36YcBguYBCH' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/auth/logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AuthController@logout',
        'controller' => 'App\\Http\\Controllers\\Auth\\AuthController@logout',
        'namespace' => NULL,
        'prefix' => 'api/v1/auth',
        'where' => 
        array (
        ),
        'as' => 'generated::Mq1na36YcBguYBCH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NL2Bg8II5Rk4mWTL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/auth/refresh',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AuthController@refresh',
        'controller' => 'App\\Http\\Controllers\\Auth\\AuthController@refresh',
        'namespace' => NULL,
        'prefix' => 'api/v1/auth',
        'where' => 
        array (
        ),
        'as' => 'generated::NL2Bg8II5Rk4mWTL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FNiTR6tIPjNsmuSy' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\Api\\V1\\ProfileController@show',
        'controller' => 'App\\Http\\Controllers\\Api\\V1\\ProfileController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1/profile',
        'where' => 
        array (
        ),
        'as' => 'generated::FNiTR6tIPjNsmuSy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::q6i6nf3PD1Se7hLr' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'api/v1/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\Api\\V1\\ProfileController@update',
        'controller' => 'App\\Http\\Controllers\\Api\\V1\\ProfileController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1/profile',
        'where' => 
        array (
        ),
        'as' => 'generated::q6i6nf3PD1Se7hLr',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::baMBjqOWaGLP6hTZ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/profile/change-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\Api\\V1\\ProfileController@changePassword',
        'controller' => 'App\\Http\\Controllers\\Api\\V1\\ProfileController@changePassword',
        'namespace' => NULL,
        'prefix' => 'api/v1/profile',
        'where' => 
        array (
        ),
        'as' => 'generated::baMBjqOWaGLP6hTZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::2bbrvcQ7ZM1PqSyJ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\Api\\V1\\NotificationController@index',
        'controller' => 'App\\Http\\Controllers\\Api\\V1\\NotificationController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::2bbrvcQ7ZM1PqSyJ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8YiUSl6ShOMnpUOs' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/products',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ProductController@index',
        'controller' => 'App\\Http\\Controllers\\ProductController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1/products',
        'where' => 
        array (
        ),
        'as' => 'generated::8YiUSl6ShOMnpUOs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jQR1kSehmkU8cxI4' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/products',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ProductController@store',
        'controller' => 'App\\Http\\Controllers\\ProductController@store',
        'namespace' => NULL,
        'prefix' => 'api/v1/products',
        'where' => 
        array (
        ),
        'as' => 'generated::jQR1kSehmkU8cxI4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dOEBgkjoNtDCbMtj' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/products/search',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ProductController@search',
        'controller' => 'App\\Http\\Controllers\\ProductController@search',
        'namespace' => NULL,
        'prefix' => 'api/v1/products',
        'where' => 
        array (
        ),
        'as' => 'generated::dOEBgkjoNtDCbMtj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YS66yz2vMYIOUv0a' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/products/barcode/{barcode}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ProductController@getByBarcode',
        'controller' => 'App\\Http\\Controllers\\ProductController@getByBarcode',
        'namespace' => NULL,
        'prefix' => 'api/v1/products',
        'where' => 
        array (
        ),
        'as' => 'generated::YS66yz2vMYIOUv0a',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EWyGcgl0gyC4FGq7' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/products/barcode/{barcode}/nutritional',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ProductController@nutritionalInfo',
        'controller' => 'App\\Http\\Controllers\\ProductController@nutritionalInfo',
        'namespace' => NULL,
        'prefix' => 'api/v1/products',
        'where' => 
        array (
        ),
        'as' => 'generated::EWyGcgl0gyC4FGq7',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vERsC0IZdv4213dz' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/products/barcode/{barcode}/suggestions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ProductController@suggestions',
        'controller' => 'App\\Http\\Controllers\\ProductController@suggestions',
        'namespace' => NULL,
        'prefix' => 'api/v1/products',
        'where' => 
        array (
        ),
        'as' => 'generated::vERsC0IZdv4213dz',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::JGtzsaYroSBHCF7H' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/products/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ProductController@show',
        'controller' => 'App\\Http\\Controllers\\ProductController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1/products',
        'where' => 
        array (
        ),
        'as' => 'generated::JGtzsaYroSBHCF7H',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dUNYsIBtk55lCSoN' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'api/v1/products/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ProductController@update',
        'controller' => 'App\\Http\\Controllers\\ProductController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1/products',
        'where' => 
        array (
        ),
        'as' => 'generated::dUNYsIBtk55lCSoN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vUdoOr1v3WZd2AuO' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/products/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ProductController@destroy',
        'controller' => 'App\\Http\\Controllers\\ProductController@destroy',
        'namespace' => NULL,
        'prefix' => 'api/v1/products',
        'where' => 
        array (
        ),
        'as' => 'generated::vUdoOr1v3WZd2AuO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::T31foYwdcZsIIBRH' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/pantries',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@index',
        'controller' => 'App\\Http\\Controllers\\PantryController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::T31foYwdcZsIIBRH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::14pxibcz1ZVqfYnW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/pantries',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@store',
        'controller' => 'App\\Http\\Controllers\\PantryController@store',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::14pxibcz1ZVqfYnW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Eii3oQOprTBkJXBx' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/pantries/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@show',
        'controller' => 'App\\Http\\Controllers\\PantryController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::Eii3oQOprTBkJXBx',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::iWT02jMeHoJ4M0gg' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'api/v1/pantries/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@update',
        'controller' => 'App\\Http\\Controllers\\PantryController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::iWT02jMeHoJ4M0gg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nZC4A8PyztcEDP5E' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/pantries/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@destroy',
        'controller' => 'App\\Http\\Controllers\\PantryController@destroy',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::nZC4A8PyztcEDP5E',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::K0VXxICgaVZ847Od' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/pantries/{id}/items',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@addItem',
        'controller' => 'App\\Http\\Controllers\\PantryController@addItem',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::K0VXxICgaVZ847Od',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LTjrJ3qtURk0YfMH' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'api/v1/pantries/{id}/items/{itemId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@updateItem',
        'controller' => 'App\\Http\\Controllers\\PantryController@updateItem',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::LTjrJ3qtURk0YfMH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::l7GUb95trfW9wr3f' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/pantries/{id}/items/{itemId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@deleteItem',
        'controller' => 'App\\Http\\Controllers\\PantryController@deleteItem',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::l7GUb95trfW9wr3f',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Xz4O9kCDntnurwqm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/pantries/{id}/notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@notifications',
        'controller' => 'App\\Http\\Controllers\\PantryController@notifications',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::Xz4O9kCDntnurwqm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::W2GNfoLN95AZqzUB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/pantries/{id}/share',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@share',
        'controller' => 'App\\Http\\Controllers\\PantryController@share',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::W2GNfoLN95AZqzUB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ecpkLiaHIYIiNfnL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/pantries/shared/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@joinShared',
        'controller' => 'App\\Http\\Controllers\\PantryController@joinShared',
        'namespace' => NULL,
        'prefix' => 'api/v1/pantries',
        'where' => 
        array (
        ),
        'as' => 'generated::ecpkLiaHIYIiNfnL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ICsffPoErj5V1u4U' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/shopping-lists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@index',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@index',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::ICsffPoErj5V1u4U',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::hjUUmiDrXmswDl5M' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/shopping-lists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@store',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@store',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::hjUUmiDrXmswDl5M',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::iyW8DnfqJLG0YBAk' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/shopping-lists/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@show',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@show',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::iyW8DnfqJLG0YBAk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::3eZ4UqXma6N4Tlz9' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'api/v1/shopping-lists/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@update',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@update',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::3eZ4UqXma6N4Tlz9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GcapKbmTVyDrvQEK' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/shopping-lists/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@destroy',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@destroy',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::GcapKbmTVyDrvQEK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::74UtIUXE7tcdXjEO' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/shopping-lists/{id}/items',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@addItem',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@addItem',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::74UtIUXE7tcdXjEO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::F8OuiaEtevckhvKo' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'api/v1/shopping-lists/{id}/items/{itemId}/purchased',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@markItemPurchased',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@markItemPurchased',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::F8OuiaEtevckhvKo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FDyBzR6LUdreVowO' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'api/v1/shopping-lists/{id}/items/{itemId}/unpurchased',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@unmarkItemPurchased',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@unmarkItemPurchased',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::FDyBzR6LUdreVowO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5ZnNu4YLyUel4k56' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'api/v1/shopping-lists/{id}/items/{itemId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@deleteItem',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@deleteItem',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::5ZnNu4YLyUel4k56',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::68dV1ooMmIsCLDwJ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/shopping-lists/{id}/complete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@complete',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@complete',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::68dV1ooMmIsCLDwJ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kppghxDbMawyizgR' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/shopping-lists/{id}/move-to-pantry',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@moveToPantry',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@moveToPantry',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::kppghxDbMawyizgR',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dvESHhYcFSrPMYS9' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/shopping-lists/{id}/suggestions',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@suggestions',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@suggestions',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::dvESHhYcFSrPMYS9',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::zO1rsbMkwbgoKxnw' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/shopping-lists/{id}/share',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@share',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@share',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::zO1rsbMkwbgoKxnw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rqVLd6Rzead4fc0h' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/v1/shopping-lists/shared/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@joinShared',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@joinShared',
        'namespace' => NULL,
        'prefix' => 'api/v1/shopping-lists',
        'where' => 
        array (
        ),
        'as' => 'generated::rqVLd6Rzead4fc0h',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4QKtk80Go4vsWk3T' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/pantry/shared/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\PantryController@showSharedByToken',
        'controller' => 'App\\Http\\Controllers\\PantryController@showSharedByToken',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::4QKtk80Go4vsWk3T',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xKWHBfcWyqfcqDl2' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/v1/shopping-list/shared/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\ShoppingListController@showSharedByToken',
        'controller' => 'App\\Http\\Controllers\\ShoppingListController@showSharedByToken',
        'namespace' => NULL,
        'prefix' => 'api/v1',
        'where' => 
        array (
        ),
        'as' => 'generated::xKWHBfcWyqfcqDl2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vBFdTIIiGJxDx28L' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/test',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:173:"fn() => \\response()->json([
    \'success\'   => true,
    \'message\'   => \'NutriCasa API is working!\',
    \'version\'   => \'1.0.0\',
    \'timestamp\' => \\now()->toISOString(),
])";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000003130000000000000000";}}',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::vBFdTIIiGJxDx28L',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::BorMqwWktXMe80rB' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:177:"function () {
    return \\response()->json([
        \'app\' => \'NutriCasa API\',
        \'version\' => \'1.0.0\',
        \'status\' => \'running\',
        \'docs\' => \'/api/v1\'
    ]);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000003220000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::BorMqwWktXMe80rB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
