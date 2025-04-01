<?php

namespace App\Http\Controllers;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'user_id'=>'required|exists:users,id',
            'message'=>'required|string|max:225',
        ]);

        $notification=Notification::create([
            'user_id'=>$request->user_id,
            'message'=>$request->message,
        ]);
        return response()->json(['message' => 'Notification created', 'notification' => $notification], 201);
    }

    public function index(){
        $notifications=Notification::where('user_id',Auth::id())->latest()->get();
        return response()->json($notifications);
    }

    public function show($id){
        $notification=Notification::where('user_id',Auth::id())->findOrFail($id);
        return response()->json($notification);
    }

    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();
        return response()->json(['message' => 'Notification deleted']);
    }


    }

