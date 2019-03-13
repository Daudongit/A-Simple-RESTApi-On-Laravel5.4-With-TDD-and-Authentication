<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{   
    use RecordsActivityTrait;

    protected $fillable = ['name','continent'];
}
