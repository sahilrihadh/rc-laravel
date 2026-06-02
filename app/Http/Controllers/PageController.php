<?php
// app/Http/Controllers/PageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use App\Models\Poll;
use App\Models\PollVote;
use App\Models\Reaction;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    // Remove the constructor completely
    
    // Webcast Page
    public function webcast()
    {
        $user = Auth::user();
        return view('pages.webcast', compact('user'));
    }
    
    // Previous Sessions Page
    public function previousSessions()
    {
        return view('pages.previous-sessions');
    }
    
    // Player Page (Video Stream)
    public function player()
    {
        return view('pages.player');
    }
    
    // Rest of your methods remain the same...
    public function submitQuestion(Request $request)
    {
        // Your existing code
    }
    
    public function getQuestions()
    {
        // Your existing code
    }
    
    public function checkPoll()
    {
        // Your existing code
    }
    
    public function submitVote(Request $request)
    {
        // Your existing code
    }
    
    public function storeReaction(Request $request)
    {
        // Your existing code
    }
    
    public function trackActivity(Request $request)
    {
        // Your existing code
    }
}