<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get all of the subjects for the Grade
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workings(): HasMany
    {
        return $this->hasMany(Working::class, 'subject_id');
    }
}
