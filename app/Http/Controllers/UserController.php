<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    /**
     * ユーザページ表示
     */
    public function show(string $name)
    {
        // リレーション先の、さらにリレーション先をEagerロード
        // つまり、リレーション先である記事に紐づくuser,likes,tagsをEagerロード
        $user = User::where('name', $name)->first()->load(['articles.user', 'articles.likes', 'articles.tags']);

        $articles = $user->articles->sortByDesc('created_at');

        return view('users.show', [
            'user' => $user,
            'articles' => $articles,
        ]);
    }

    /**
     * 指定の$nameユーザがいいねしている記事一覧表示
     */
    public function likes(string $name)
    {
        // リレーション先の、さらにリレーション先をEagerロード
        // 'likes.likes' で解説
        // 一つ目のlikesはuser.likesのことでユーザがいいねしている記事データ（a）
        // 二つ目のlikesはarticle.likesのことで記事にいいねしているユーザデータ（b）
        // 「いいねした記事一覧」では、ある1人のユーザーがいいねした記事の一覧が表示される。ここで、(a)が必要。
        // それぞれの記事については、いいね数を表示している。これを求めるのに、いいねしたユーザーモデルの数、つまり(b)の数をカウントしている。
        $user = User::where('name', $name)->first()->load(['likes.user', 'likes.likes', 'likes.tags']);

        $articles = $user->likes->sortByDesc('created_at');

        return view('users.likes', [
            'user' => $user,
            'articles' => $articles,
        ]);
    }

    /**
     * 指定の$nameユーザがフォローしているユーザ一覧を表示
     */
    public function followings(string $name)
    {
        // リレーション先の、さらにリレーション先をEagerロード
        // ユーザーモデルのリレーション先のフォロー中ユーザーの、さらにリレーション先の、フォロワーをEagerロード
        $user = User::where('name', $name)->first()->load('followings.followers');

        $followings = $user->followings->sortByDesc('created_at');

        return view('users.followings', [
            'user' => $user,
            'followings' => $followings,
        ]);
    }
    
    /**
     * 指定の$nameユーザのフォロワー一覧を表示
     */
    public function followers(string $name)
    {
        // リレーション先の、さらにリレーション先をEagerロード
        // ユーザーモデルのリレーション先のフォロワーの、さらにリレーション先の、フォロワーをEagerロード
        $user = User::where('name', $name)->first()->load('followers.followers');

        $followers = $user->followers->sortByDesc('created_at');

        return view('users.followers', [
            'user' => $user,
            'followers' => $followers,
        ]);
    }

    /**
     * 指定の$nameユーザをフォローする
     */
    public function follow(Request $request, string $name)
    {
        $user = User::where('name', $name)->first();

        if ($user->id === $request->user()->id) {
            return abort('404', 'Cannot follow yourself.');
        }

        // 1人のユーザーがあるユーザーを複数回重ねてフォローできないようにするためdetachで削除
        $request->user()->followings()->detach($user);
        $request->user()->followings()->attach($user);

        return ['name' => $name];
    }

    /**
     * 指定の$nameユーザをフォロー解除する
     */
    public function unfollow(Request $request, string $name)
    {
        $user = User::where('name', $name)->first();

        if ($user->id === $request->user()->id) {
            return abort('404', 'Cannot follow yourself.');
        }

        $request->user()->followings()->detach($user);

        return ['name' => $name];
    }
}
