<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'body', 'user_id', 'post_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    public function votes()
    {
        return $this->belongsToMany('App\User', 'votes', 'comment_id', 'user_id')
                    ->withPivot('vote');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function replyTo()
    {
        return $this->belongsTo('App\Comment', 'comment_id');
    }
}
