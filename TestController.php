<?php

class TestController
{

    public function index()
    {
        echo "Home Page";
    }

    public function products()
    {
        echo 'Product List';
    }

    public function productDetail($id)
    {
        echo "<pre>";
        print_r($id);
        echo "</pre>";
    }

    public function match()
    {
        echo "Match";
    }
}

?>