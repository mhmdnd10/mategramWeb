<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request,$postId){
        $request->validate([
            'content'=>'required|string|max:225'
        ]);
        $post=Post::findOrFail($postId);
        $comment=Comment::create([
            'user_id'=>Auth::id(),
            'post_id'=>$post->id,
            'content'=>$request->content,
        ]);

        return response()->json([
            $comment,201
        ]);
    }

    public function index($postId){
        Post::findOrFail($postId);
        $comments=Comment::with('user')->where('post_id',$postId)->latest()->get();
        return response()->json($comments);
    }

    public function show($postId,$id){
        Post::findOrFail($postId);
        $comment=Comment::where('post_id',$postId)->where('id',$id)->with('user')->firstOrFail();
        return response()->json($comment);
    }

    public function destroy($postId,$id){
        Post::findOrFail($postId);
        $comment=Comment::where('post_id',$postId)->where('id',$id)->where('user_id',Auth::id())->firstOrFail();
        $comment->delete();
        return response()->json(['message'=>'Comment deleted successfully']);
    }
}
