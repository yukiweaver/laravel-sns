<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name'
    ];

    public function getHashtagAttribute():string
    {
        return '#' . $this->name;
    }

    /**
     * タグに紐づく記事を取得
     */
    public function articles():BelongsToMany
    {
        return $this->belongsToMany('App\Article')->withTimestamps();
    }
}
