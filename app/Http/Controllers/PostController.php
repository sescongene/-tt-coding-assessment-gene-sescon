<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function index()
    {
        return PostResource::collection(Post::all());
    }

    public function store(CreatePostRequest $request)
    {
        $data = $request->validated();
        $post = Post::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'user_id' => auth()->id(),
        ]);

        $tags = collect($data['tags'])->map(function ($tag) {
            return Tag::firstOrCreate(['name' => $tag]);
        })->pluck('id');

        $post->tags()->attach($tags);

        return PostResource::make($post);
    }


    public function show(Post $post)
    {
        return PostResource::make($post);
    }

    public function delete(Post $post)
    {
        $post->delete();
        return response(null, 204);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();
        $post->update([
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        $tags = collect($data['tags'])->map(function ($tag) {
            return Tag::firstOrCreate(['name' => $tag]);
        })->pluck('id');

        $post->tags()->sync($tags);

        return PostResource::make($post);
    }
}
