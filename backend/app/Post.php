<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title', 'body', 'slug', 'category_id', 'author_id', 'caption'
    ];

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

    public function scopeUnPublished($query)
    {
        $query->where('published', 0);
    }

    public function scopePublished($query)
    {
        $query->where('published', 1);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * 1. we will get the tags from DB that matches the given tags array
     * 2. we will reduce the extracted tags array from the given tags array
     * 3. we will insert the result tags array into the DB && get the inserted ids
     * 4. we will use sync to build the relationship between the posts & tags
     */
    public function addTags($arr)
    {
        $arr = (array) $arr;
        $tagsIds = [];
        $tags = [];

        foreach ($arr as $value) {
            $tags[str_slug($value)] = $value;
        }

        #1 slugs of the already existing tags
        $exist = \DB::table('tags')->select(['id', 'slug'])->whereIn('slug', array_keys($tags))->get();
        $exist->transform(function ($item) use (&$tagsIds) {
            $tagsIds[] = $item->id;
            return $item->slug;
        });

        #2 slugs of the received new tags
        $result = array_diff(array_keys($tags), $exist->toArray());

        #3 array of (name, slug) for the received new tags
        $prepareTagsArr = array_map(function ($item) use ($tags) {
            return [
                'name' => $tags[$item],
                'slug' => $item
            ];
        }, $result);

        $insertedTags = $this->tags()->createMany(array_values($prepareTagsArr));

        #4 the last modification for the $tagsIds
        array_map(function ($item) use (&$tagsIds) {
            $tagsIds[] = $item->id;
        }, $insertedTags);

        $this->tags()->sync($tagsIds);
    }

}
