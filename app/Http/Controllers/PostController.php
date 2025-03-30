<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'description'=>'nullable|string|max:225',
            'media_url'=>'nullable|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:10240',
        ]);

        $mediaPath = $request->file('media_url') ? $request->file('media_url')->store('posts', 'public') : null;
        $post= Post::create([
            'user_id'=>Auth::id(),
            'description'=>$request->description,
            'media_url'=>$mediaPath,
        ]);
        return response()->json($post, 201);
      
    }

    public function index(){
        $posts=Post::with('user')->latest()->get();
        return response()->json($posts);
    }

    public function show($id){
        $post=Post::with('user')->findOrFail($id);
        return response()->json($post);
    }

    public function destroy($id){
        $post=Post::where('id',$id)->where('user_id',Auth::id())->firstOrFail();
        $post->delete();
        return response()->json([
            'message'=>'Post deleted successfully'
        ]);
    }
}
