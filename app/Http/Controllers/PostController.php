<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Tampilkan semua post milik user login
     */
    public function index()
    {
        $posts = auth()->user()->posts;

        return view('posts.index', compact('posts'));
    }

    /**
     * Form create post
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Simpan post baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        auth()->user()->posts()->create([
            'title' => $request->title,
            'content' => $request->content,
            'status' => 'published',
        ]);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post berhasil dibuat');
    }

        /**
     * Form edit post
     */
    public function edit(Post $post)
    {
        // Pastikan hanya owner yang bisa edit
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        return view('posts.edit', compact('post'));
    }

    /**
     * Update post
     */
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post berhasil diupdate');
    }

    /**
     * Hapus post
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post berhasil dihapus');
    }


}
