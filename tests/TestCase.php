<?php

namespace Tests;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $user;

    protected function setUp()
    {
        parent::setUp();

        \DB::statement('PRAGMA foreign_keys=on;');

        $this->disableExceptionHandling();
    }


    // Hat tip, @adamwathan.
    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);

        $this->app->instance(ExceptionHandler::class, new TestHandler());
    }

    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);

        return $this;
    }

    /**
     * Create user and get token
     * @return string
     */
    protected function authenticate(){
        
        $user = factory('App\User')->create();

        $token = JWTAuth::fromUser($user);

        request()->headers->set('Authorization','Bearer '. $token);
        
        $this->user = $user;

        return $token;
    }
}
