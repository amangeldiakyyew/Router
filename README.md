### Router
Simple Routing System for PHP
##Features
-Supports GET, POST, PUT, PATCH, DELETE request methods
-Supports Controllers [Ex:TestController@test]
-Group Routing
-Helper methods for listing and displaying all kind of routes and url
-Displaying routes by it's name
-Namespace support
-Debug mode 

## Usage
include Router class and route file
```php
require_once 'Router.php';
require_once 'routes.php';
```

#define routes in route.php

don't forget edit .htaccess files
```htaccess
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?page=$1 [QSA,L]
```

##Helpers
```php
Router::getRoutes();
Router::getCurrent();
Router::getCurrentRouteName();
Router::getCurrentUrl();
Router::getCurrentUri();
Router::getCurrentController();
Router::getCurrentMethod();
```



