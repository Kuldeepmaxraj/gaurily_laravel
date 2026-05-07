<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $blogs = Blog::published()
            ->when($category, fn($q) => $q->where('category', $category))
            ->latest('published_at')
            ->paginate(9);

        $categories = Blog::published()->distinct()->pluck('category')->filter()->values();
        $recent     = Blog::published()->latest('published_at')->take(3)->get();

        return view('pages.blogs', compact('blogs', 'categories', 'category', 'recent'));
    }

    public function show(Blog $blog)
    {
        abort_unless($blog->is_published, 404);
        $related = Blog::published()
            ->where('id', '!=', $blog->id)
            ->where('category', $blog->category)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('pages.blog-show', compact('blog', 'related'));
    }
}