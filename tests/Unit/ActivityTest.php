<?php

namespace Tests\Unit;

use App\Activity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use JWTAuth;

class ActivityTest extends TestCase
{
    use DatabaseMigrations;

     
     public function test_that_an_activity_is_recorded_when_a_country_is_created()
     {
        //Authenticate and create country
        $token = $this->authenticate();

        $country = factory('App\Country')->create();
        
         $this->assertDatabaseHas('activities', [
             'type' => 'created_country',
             'user_id' => $this->user->id,
             'subject_id' => $country->id,
             'subject_type' => 'App\Country'
         ]);
 
          $activity = Activity::first();

         $this->assertEquals($activity->subject->id, $country->id);
     }

    public function test_that_an_activity_is_recorded_when_a_country_is_updated()
    {
        //Authenticate and create country
        $token = $this->authenticate();

        $country = factory('App\Country')->create();

        $country->update(['name'=>'Nigeria']);

        $this->assertEquals(2, Activity::count());
    }

    public function test_that_an_activity_is_recorded_when_a_country_is_deleted()
    {
        //Authenticate and create country
        $token = $this->authenticate();

        $country = factory('App\Country')->create();

        $country->delete();
        
        $this->assertEquals(2, Activity::count());
    }
}