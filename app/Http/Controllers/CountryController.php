<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;

class CountryController extends Controller
{
    /**
     * Display a listing of the country resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Country::all();
    }


    /**
     * Store a newly created country resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate
        $this->validate(
            $request,
            [
                'name' => 'required|string|unique:countries',
                'continent' => 'required|string'
            ]
        );

        //Create country 
        $country = Country::create(
            $request->only(['name','continent'])
        );

        //Return json of country
        return response()->json($country,201);
    }

    /**
     * Update the specified country resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Country $country)
    {   
        //Validate
        $this->validate(
            $request,
            [
                'name' => 'required|string|unique:countries',
                'continent' => 'required|string'
            ]
        );
        
        //Update and return
        $country->update($request->only('name','continent'));

       return response()->json($country,200);
    }

    /**
     * Remove the specified country resource from storage.
     *
     * @param  Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
        if($country->delete()){
            return response()->json(null,204); 
        }
    }
}
