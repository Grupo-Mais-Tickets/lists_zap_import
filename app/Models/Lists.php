<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Recipients;

class Lists extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'company_id'
    ];

    public function recipients()
    {
        return $this->belongsToMany(Recipients::class, 'lists_recipients', 'lists_id', 'recipients_id');
    }
}