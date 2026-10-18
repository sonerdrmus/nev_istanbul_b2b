<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function show(BlogPost $post): View
    {
        if (! $post->isPublic()) {
            abort(404);
        }

        return view('store.blog-article', [
            'post' => $post,
        ]);
    }
}
