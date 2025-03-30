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
}
