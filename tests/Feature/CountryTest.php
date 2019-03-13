<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CountryTest extends TestCase
{
    use DatabaseMigrations;

    public function test_that_an_authenticated_user_can_get_all_countries(){
        //Authenticate and create country
        $token = $this->authenticate();

        $country = factory('App\Country')->create();

        //call route and assert response
        $response = $this->json(
            'GET',
            route('countries.index'),
            [],['Authorization' => 'Bearer '. $token]
        );

        $response->assertStatus(200);

        $response->assertJsonStructure(
            [ array_keys($country->toArray()) ]
        );

        //Assert the count is 1 and the name of the first country correlates
        $this->assertEquals(1,count($response->json()));

        $this->assertEquals($country->name,$response->json()[0]['name']);
    }

    public function test_that_an_authenticated_user_can_create_new_country()
    {
        //Get token
        $token = $this->authenticate();

        $country = factory('App\Country')->make();

        $response = $this->json(
            'POST',
            route('countries.store'),
            $country->toArray(),
            ['Authorization' => 'Bearer '. $token]
        );

        // Check status, structure and data
        $response->assertStatus(201)->assertJsonStructure(
            array_merge(array_keys($country->toArray(),['id']))
        )->assertJsonFragment($country->toArray());
    }

    public function test_that_an_authenticated_user_can_update_country(){
        
        $token = $this->authenticate();

        $country = factory('App\Country')->create();

        $makeCountry = factory('App\Country')->make();

        //call route and assert response
        $response = $this->json(
            'PUT',
            route('countries.update',['country' => $country->id]),
            $makeCountry->toArray(),
            ['Authorization' => 'Bearer '. $token]
        );

        $response->assertStatus(200);

        //Assert name is the new name
        $this->assertEquals($makeCountry->name,$country->fresh()->name);
    }

    public function test_that_an_authenticated_user_can_delete_country(){

        $token = $this->authenticate();
       
        $country = factory('App\Country')->create();

        $response = $this->json(
            'DELETE',
            route('countries.destroy',['country' => $country->id]),
            [],['Authorization' => 'Bearer '. $token]
        );

        $response->assertStatus(204);
    }

    //Validation
    /** @dataProvider countryDataSet */
    public function test_that_a_country_requires_a_valid_name_and_continent($countryData)
    {  
        $response = $this->publishCountry($countryData);

        $response->assertStatus(422);

        $jsonStructure = ['continent'];

        if($countryData['name'] == null || $countryData['name'] == 999)
        {
            $jsonStructure = ['name'];
        }

        if($countryData['name'] == null && $countryData['continent'] == null)
        {
            $jsonStructure = ['name','continent'];
        }

        if($countryData['name'] == 999 && $countryData['continent'] == 999)
        {
            $jsonStructure = ['name','continent'];
        }

        $response->assertJsonStructure($jsonStructure);
    }

    public function test_that_a_country_requires_a_unique_name()
    {  
        $country = factory('App\Country')->create();

        $response = $this->publishCountry(['name' => $country->name]);

        $response->assertStatus(422);

        $response->assertJsonStructure(['name']);
    }

    public function countryDataSet()
    {
        return [
           'nullName'=> [['name'=>null,'continent'=>'Africa']],
           'nullContinent'=> [['name'=>'Nigeria','continent'=>null]],
           'numericName'=> [['name'=>999,'continent'=>'Africa']],
           'numericContinent'=> [['name'=>'Nigeria','continent'=>999]],
           'nullBoth'=> [['name'=>null,'continent'=>null]],
           'numericBoth'=> [['name'=>999,'continent'=>999]],
        ];
    }

    protected function publishCountry($overrides = [])
    {
        $this->withExceptionHandling();

        $token = $this->authenticate();

        $country = factory('App\Country')->make($overrides);

        return $this->json(
            'POST',
            route('countries.store'),
            $country->toArray(),
            ['Authorization' => 'Bearer '. $token]
        );
    }
}
