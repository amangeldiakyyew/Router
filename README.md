### Router
Simple Routing System for PHP
##Features
-Supports GET, POST, PUT, PATCH, DELETE request methods\
-Supports Controllers [Ex:TestController@test]\
-Group Routing\
-Helper methods for listing and displaying all kind of routes and url\
-Displaying routes by it's name\
-Namespace support\
-Debug mode\

## Usage
include Router class and route file
```php
require_once 'Router.php';
require_once 'routes.php';
```

*define routes in route.php

don't forget edit .htaccess files
```htaccess
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?page=$1 [QSA,L]
```

## Helpers
```php
Router::getRoutes();
Router::getCurrent();
Router::getCurrentRouteName();
Router::getCurrentUrl();
Router::getCurrentUri();
Router::getCurrentController();
Router::getCurrentMethod();
```

## Example Usage
```php
Router::get('/', 'TestController@index')->name('index');
Router::match(['POST', 'PATCH'], '/match', 'TestController@match')->name('match');
Router::group(['prefix' => 'product', 'name' => 'product.'], function () {
    Router::get('/{id}', 'TestController@productDetail')->name('productDetail');
    Router::get('/list-all', 'TestController@products')->name('products');
});
Router::get('/test/{name}/{id}', function ($name, $id) {
    echo $name . "-" . $id;
})->name('test');
Router::get('/get-route-by-name', function () {
    echo Router::route('product.productDetail', [2]);
})->name('get-route-by-name');
Router::get('/get-routes', function () {
    echo "<pre>";
    print_r(Router::getRoutes());
})->name('get-routes');
Router::run();
```
