<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lists;
use App\Domain\Schedules\Entity\Schedules;

class Recipients extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'birthdate'
    ];

    public function lists()
    {
        return $this->belongsToMany(Lists::class, 'lists_recipients', 'recipients_id', 'lists_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedules::class, 'recipient_id');
    }
}