<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = ['name', 'name_kh', 'code', 'dean', 'email', 'est_year', 'description', 'is_active'];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function majors()
    {
        return $this->hasManyThrough(Major::class, Department::class);
    }
}
