<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title', 'body', 'slug', 'category_id', 'author_id', 'caption'
    ];

    //================ relationship ================
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function author()
    {
        return $this->belongsTo('App\User');
    }

    public function recommendations()
    {
        return $this->belongsToMany(
            'App\User', 'recommendations', 'post_id', 'user_id'
        );
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    //================ local scopes ================
    public function scopeUnPublished($query)
    {
        $query->where('published', 0);
    }

    public function scopePublished($query)
    {
        $query->where('published', 1);
    }
}
