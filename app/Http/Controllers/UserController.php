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
        $user = User::where('name', $name)->first();

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
        $user = User::where('name', $name)->first();

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
        $user = User::where('name', $name)->first();

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
        $user = User::where('name', $name)->first();

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
