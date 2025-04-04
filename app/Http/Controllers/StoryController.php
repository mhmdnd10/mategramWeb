<?php

namespace App\Http\Controllers;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class StoryController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'media_url'=>'required|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:10240',
        ]);
        $mediaPath = $request->file('media_url') ? $request->file('media_url')->store('stories', 'public') : null;

        $story= Story::create([
            'user_id'=>auth()->id(),
            'media_url'=> $mediaPath,
            'expired_at'=>now()->addHours(24),
        ]);

        return response()->json([
            'message'=>'Story created successfully',
            'story'=>$story,
        ]);
    }

    public function index(){
        $stories=Story::with('user')->where('expired_at','>',now())->orderBy('created_at','desc')->get();
        return response()->json($stories);
    }

    public function userStories($userId){
        $stories=Story::where('user_id',$userId)->where('expired_at','>',now())->orderBy('created_at', 'desc')->get();
        return response()->json($stories);

    }

    public function destroy($storyId){
        $story=Story::where('id',$storyId)->where('user_id',Auth::id())->first();
        if(!$story){
            return response()->json(['message'=>'Story not found'],404);
        }
        $story->delete();
        return response()->json(['message'=>'Story deleted successfully']);
    }

    

}
