<?php

namespace App\Models\Movies;

use App\DTO\Movies\MinMaxAwardIntervalsDTO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'title',
        'studios',
        'winner',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function producers()
    {
        return $this->belongsToMany(Producer::class, 'producers_movies');
    }
}
