<?php

namespace App\Http\Controllers;
use App\Models\Follow;
use App\Models\User;
use App\Models\Notification;
use App\Events\NewNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request){
        $request->validate([
            'user_id'=>'required|exists:users,id',
        ]);

        if(!$request->user_id){
            return response()->json(['message'=>'This user doesnot exist']);
        }

        if(Auth::id()==$request->user_id){
            return response()->json(['message' => 'You cannot follow yourself'], 400);
        }
        if(Follow::where('follower_id',Auth::id())->where('following_id',$request->user_id)->exists()){
            return response()->json(['message' => 'You are already following this user'], 400);
        }

        Follow::create([
            'follower_id'=>Auth::id(),
            'following_id'=>$request->user_id,
        ]);
        $notification = Notification::create([
            'user_id' => $request->user_id,
            'message' => Auth::user()->name . ' started following you.',
        ]);
    
       
        $notification = Notification::find($notification->id);
        event(new NewNotification($notification));
        broadcast(new NewNotification($notification))->toOthers();
        return response()->json(['message' => 'You are now following this user']);
    }

    public function unfollow(Request $request){
        $request->validate([
            'user_id'=>'required|exists:users,id',
        ]);

        $follow=Follow::where('follower_id',Auth::id())->where('following_id',$request->user_id)->first();
        if(Auth::id()==$request->user_id){
            return response()->json(['message' => 'You can not unfollow yourself'], 400);
        }
        if(!$follow){
            return response()->json(['message' => 'You are not following this user'], 400);

        }
        $follow->delete();
        return response()->json(['message' => 'You have unfollowed this user']);
    }

    public function followers($userId){
        User::findOrFail($userId);
        $followers=Follow::where('following_id',$userId)->get();
        return response()->json($followers);
    }

    public function following($userId){
        $following=Follow::where('follower_id',$userId)->get();
        return response()->json($following);
    }

    public function followerCount($userId){
        User::findOrFail($userId);
        $count=Follow::where('following_id',$userId)->count();
        return response()->json(['followers count'=>$count]);
    }

    public function followingCount($userId){
        User::findOrFail($userId);
        $count=Follow::where('follower_id',$userId)->count();
        return response()->json(['following count'=>$count]);
    }
}
