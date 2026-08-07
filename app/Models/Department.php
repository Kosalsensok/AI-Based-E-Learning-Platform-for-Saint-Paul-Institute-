<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['faculty_id', 'name', 'name_kh', 'code', 'head', 'email', 'description', 'is_active'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function majors()
    {
        return $this->hasMany(Major::class);
    }
}
