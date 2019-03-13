<?php

namespace Tests;

use App\Exceptions\Handler;

class TestHandler extends Handler{
    public function __construct() {}
    public function report(\Exception $e) {}
    public function render($request, \Exception $e) {
        throw $e;
    }
}