<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Activity;

class ActivityController extends Controller
{   

    /**
     * @SWG\Get(
     *     path="/activities",
     *     description="Return a user's first and last name",
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Missing Data"
     *     )
     * )
     */

    /**
     * Display a listing of the country resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Activity::paginate(20);
    }
}