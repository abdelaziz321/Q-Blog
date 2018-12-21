<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'slug', 'description', 'moderator'];

    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    public function moderator()
    {
        return $this->belongsTo('App\User', 'moderator');
    }
}
