<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner',
        'email',
        'address',
        'website',
        'phone',
        'user_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the comments for the blog post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
