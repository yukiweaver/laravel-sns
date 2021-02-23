<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use App\Tag;
use App\Http\Requests\ArticleRequest;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Article::class, 'article'); // ArticlePolicyを使用
    }

    /**
     * 記事一覧ページ表示
     */
    public function index()
    {
        $articles = Article::all()->sortByDesc('created_at');

        return view('articles.index', ['articles' => $articles]);
    }

    /**
     * 記事投稿ページ表示
     */
    public function create()
    {
        // タグ自動補完用
        $allTagNames = Tag::all()->map(function($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.create', [
            'allTagNames' => $allTagNames,
        ]);
    }

    /**
     * 記事投稿処理
     */
    public function store(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all());
        $article->user_id = $request->user()->id; // ログイン済みのユーザが送信したリクエストならuser()が使用できる
        $article->save();

        $request->tags->each(function($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });
        return redirect()->route('articles.index');
    }

    /**
     * 記事編集ページ表示
     */
    public function edit(Article $article)
    {
        $tagNames = $article->tags->map(function($tag) {
            return ['text' => $tag->name];
        });

        // タグ自動補完用
        $allTagNames = Tag::all()->map(function($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.edit', [
            'article' => $article,
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
        ]);
    }

    /**
     * 記事更新処理
     */
    public function update(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all())->save();

        // 記事に紐づくタグデータを全削除してから、登録を行う
        $article->tags()->detach();
        $request->tags->each(function($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });

        return redirect()->route('articles.index');
    }

    /**
     * 記事削除処理
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    /**
     * 記事詳細ページ表示
     */
    public function show(Article $article)
    {
        return view('articles.show', ['article' => $article]);
    }

    /**
     * いいね処理
     */
    public function like(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id); // 1人のユーザーが同一記事に複数回重ねていいねを付けられないようにする
        $article->likes()->attach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    /**
     * いいね解除処理
     */
    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }
}
