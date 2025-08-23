<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber; 
class NewsletterController extends Controller
{
    public function subscribe(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:newsletter_subscribers,email',
    ]);

    NewsletterSubscriber::create(['email' => $request->email]);

    return back()->with('success', 'You’ve successfully subscribed!');
}

}
