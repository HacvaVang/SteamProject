<?php

class HomeController
{
    public function index(): void
    {
        require BASE_PATH . '/app/Views/products/index.php';
    }
}

?>