<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Activity;

class ActivityTest extends TestCase
{
    use DatabaseMigrations;

    public function test_that_an_authenticated_user_can_get_all_activities()
    {
        //Authenticate and create country
        $token = $this->authenticate();

        $country = factory('App\Country')->create();

        //call route and assert response
        $response = $this->json(
            'GET',
            route('activities.index'),
            [],['Authorization' => 'Bearer '. $token]
        );

        $response->assertStatus(200);

        $response = $response->json();

        //Assert the count is 1 
        $this->assertCount(1, $response['data']);
    }

    public function test_for_activities_pagination()
    {
        //Authenticate and create country
        $token = $this->authenticate();

        $countries = factory('App\Country',21)->create();

        $activities = Activity::all();

        $response = $this->json(
            'GET',
            route('activities.index'),
            [],['Authorization' => 'Bearer '. $token]
        );

        $this->assertContains(
            $activities[0]->toArray(),$response->json()['data']
        );

        $this->assertNotContains(
            $activities[20]->toArray(),$response->json()['data']
        );

        $response = $this->json(
            'GET',
            route('activities.index').'?page='.($response->json()['current_page']+1),
            [],['Authorization' => 'Bearer '. $token]
        );

        $this->assertContains(
            $activities[20]->toArray(),$response->json()['data']
        );

        $this->assertNotContains(
            $activities[0]->toArray(),$response->json()['data']
        );
    }
}