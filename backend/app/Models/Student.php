<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // The columns that are allowed to be filled in from request data.
    // Laravel blocks any column not on this list (a security feature).
    protected $fillable = ['name', 'email', 'course'];
}
