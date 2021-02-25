<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Mail\BareMail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * パスワード再設定メール送信処理
     * Illuminate/Auth/Passwords/CanResetPassword.phpからオーバーライド
     * CallしているのはIlluminate/Auth/Passwords/PasswordBroker.phpのsendResetLink()内
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token, new BareMail()));
    }

    /**
     * ユーザに紐づく記事情報を取得
     */
    public function articles():HasMany
    {
        return $this->hasMany('App\Article');
    }

    /**
     * $this（ユーザ）をフォローしているユーザ情報を返す
     */
    public function followers():BelongsToMany
    {
        return $this->belongsToMany('App\User', 'follows', 'followee_id', 'follower_id')->withTimestamps();
    }

    /**
     * $this（ユーザ）がフォローしているユーザ情報を返す
     * リレーション元のusersテーブルのidは、中間テーブルのfollower_idと紐付く
     * リレーション先のusersテーブルのidは、中間テーブルのfollowee_idと紐付く
     */
    public function followings():BelongsToMany
    {
        return $this->belongsToMany('App\User', 'follows', 'follower_id', 'followee_id')->withTimestamps();
    }

    /**
     * ユーザがいいねしている記事を取得
     */
    public function likes():BelongsToMany
    {
        return $this->belongsToMany('App\Article', 'likes')->withTimestamps();
    }

    /**
     * 指定のユーザが$this（ユーザ）をフォロー中であるか判定する
     */
    public function isFollowedBy(?User $user):bool
    {
        return $user ? (bool)$this->followers->where('id', $user->id)->count() : false;
    }

    /**
     * フォロワー数算出
     */
    public function getCountFollowersAttribute():int
    {
        return $this->followers->count();
    }

    /**
     * フォロー数算出
     */
    public function getCountFollowingsAttribute():int
    {
        return $this->followings->count();
    }
}
