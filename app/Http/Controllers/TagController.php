<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tag;

class TagController extends Controller
{
    /**
     * タグごとの記事一覧を表示
     */
    public function show(string $name)
    {
        $tag = Tag::where('name', $name)->first();
        return view('tags.show', ['tag' => $tag]);
    }
}
