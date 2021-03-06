<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    protected $fillable = [
        'title',
        'body',
    ];

    /**
     * 記事に紐づくユーザデータを取得
     */
    public function user():BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    /**
     * 記事に紐づくいいねデータを取得
     */
    public function likes():BelongsToMany
    {
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }

    /**
     * 指定のユーザが記事をいいねしているか判定
     * @param App\User $user null許可
     * @return boolean
     */
    public function isLikedBy(?User $user):bool
    {
        return $user ? (bool)$this->likes->where('id', $user->id)->count() : false;
    }

    /**
     * 一つの記事についているいいね数の合計を返す
     * @return int
     */
    public function getCountLikesAttribute():int
    {
        return $this->likes->count();
    }

    /**
     * 記事に紐づくタグデータを取得
     */
    public function tags():BelongsToMany
    {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }
}
