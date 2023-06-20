<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userId = Auth::id();
        $posts = Post::where('user_id', $userId)->paginate(5);

        return response()->json([
            'success' => true,
            'data' => $posts->toArray()
        ]);
    }

    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $post->toArray()
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'image' => 'required',
            'category_id' => 'required'
        ]);

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $request->image,
            'category_id' => $request->category_id,
            'user_id' => Auth::id()
        ]);

        if ($post)
            return response()->json([
                'success' => true,
                'data' => $post->toArray()
            ], 201);
        else
            return response()->json([
                'success' => false,
                'message' => 'Post not added'
            ], 500);
    }

    public function update(Request $request, $id)
    {
        if ($request) {
            Post::where('id', $id)->update($request->all());
            $post = Post::find($id);
            return response()->json([
                'success' => true,
                'data' => $post->toArray()
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Post can not be updated'
            ], 500);
        }
    }


    public function destroy($id)
    {
        $userId = Auth::id();
        $post = Post::where('user_id', $userId)->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        if ($post->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Post can not be deleted'
            ], 500);
        }
    }
}
