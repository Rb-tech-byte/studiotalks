<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriberController extends Controller
{
    /**
     * Display a listing of subscribers.
     */
    public function index()
    {
        $pageTitle = 'Subscribers';
        // In the absence of a model in this codebase snapshot, render the view without data
        return view('admin.subscriber.index', compact('pageTitle'));
    }

    /**
     * Show the form for sending an email to subscribers.
     */
    public function sendEmailForm()
    {
        $pageTitle = 'Send Email to Subscribers';
        return view('admin.subscriber.send_email', compact('pageTitle'));
    }

    /**
     * Remove a subscriber.
     */
    public function remove(Request $request)
    {
        // Placeholder: validate input and return with notify message
        $request->validate([
            'id' => 'required'
        ]);
        $notify[] = ['success', 'Subscriber removed (placeholder).'];
        return back()->withNotify($notify);
    }

    /**
     * Send email to subscribers.
     */
    public function sendEmail(Request $request)
    {
        // Placeholder: basic validation
        $request->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);
        $notify[] = ['success', 'Email queued to subscribers (placeholder).'];
        return back()->withNotify($notify);
    }
}
