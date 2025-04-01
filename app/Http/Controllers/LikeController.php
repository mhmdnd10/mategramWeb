<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Like;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Events\NewNotification;

use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request,$postId){
        $post=Post::findOrFail($postId);
        $existingLike=Like::where('post_id',$postId)->where('user_id',Auth::id())->first();
        if($existingLike){
            $existingLike->delete();
        return response()->json(['message' => 'Post unliked'], 200);
        }

        $like=Like::create([
            'user_id'=>Auth::id(),
            'post_id'=>$postId,
        ]);
        $notification=Notification::create([
            'user_id'=>$post->user_id,
            'message'=> Auth::user()->name . " liked your post.",
        ]);
        $notification = Notification::find($notification->id);
        event(new NewNotification($notification));
        broadcast(new NewNotification($notification))->toOthers();
        
        return response()->json(['message' => 'Post like and notification sent', $like], 201);
       
    }


    public function index($postId){
        Post::findOrFail($postId);
        $likes=Like::where('post_id',$postId)->with('user')->latest()->get();
        return response()->json($likes);
    }
    


    public function count($postId){
        Post::findOrFail($postId);
        $likeCount=Like::where('post_id',$postId)->count();
        if($likeCount==0){
            return response()->json([
                'like count'=>'0',
            ]);
        }
        return response()->json([
            'like count'=>$likeCount,
        ]);
    }
}
