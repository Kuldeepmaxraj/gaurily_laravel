<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->paginate(15);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:blogs,slug',
            'category'         => 'nullable|string|max:100',
            'author_name'      => 'nullable|string|max:100',
            'excerpt'          => 'nullable|string|max:500',
            'body'             => 'required|string',
            'tags'             => 'nullable|string|max:255',
            'meta_title'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords'    => 'nullable|string|max:255',
            'is_published'     => 'nullable',
            'published_at'     => 'nullable|date',
            'cover_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'og_image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $data['slug']        = Str::slug($data['slug'] ?? $data['title']);
        $data['author_name'] = $data['author_name'] ?: 'Gaurily Team';
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('blogs', 'public');
        }
        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('blogs/og', 'public');
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post published successfully.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:blogs,slug,' . $blog->id,
            'category'         => 'nullable|string|max:100',
            'author_name'      => 'nullable|string|max:100',
            'excerpt'          => 'nullable|string|max:500',
            'body'             => 'required|string',
            'tags'             => 'nullable|string|max:255',
            'meta_title'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords'    => 'nullable|string|max:255',
            'is_published'     => 'nullable',
            'published_at'     => 'nullable|date',
            'cover_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'og_image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $data['slug']        = Str::slug($data['slug'] ?? $data['title']);
        $data['author_name'] = $data['author_name'] ?: 'Gaurily Team';
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('cover_image')) {
            if ($blog->cover_image) Storage::disk('public')->delete($blog->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('blogs', 'public');
        }
        if ($request->hasFile('og_image')) {
            if ($blog->og_image) Storage::disk('public')->delete($blog->og_image);
            $data['og_image'] = $request->file('og_image')->store('blogs/og', 'public');
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->cover_image) Storage::disk('public')->delete($blog->cover_image);
        if ($blog->og_image)    Storage::disk('public')->delete($blog->og_image);
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted.');
    }
}