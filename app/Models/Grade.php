<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get all of the students for the Grade
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'grade_id');
    }
    /**
     * Get all of the subjects for the Grade
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workings(): HasMany
    {
        return $this->hasMany(Working::class, 'grade_id');
    }
}
