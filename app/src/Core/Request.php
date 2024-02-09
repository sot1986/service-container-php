<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    protected string $url;

    public int $count = 0;

    public function __construct()
    {
        $this->initUrl();
    }


    private function initUrl()
    {
        echo "Request initiated. <br />";

        $this->url = $_SERVER['REQUEST_URI'];
    }

    public function echoUrl()
    {
        echo "URL: " . $this->url . "<br />";
    }

    public function echoCount()
    {
        echo "Count: " . $this->count . "<br />";
    }
}
