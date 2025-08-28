<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'user_id',
        'assigned_class_id',
        'grade',
        'name',
        'email',
        'date_of_birth'
    ];
    protected $casts = [
        'grade' => 'float',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'assigned_class_id');
    }
}
