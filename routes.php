<?php

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

?>