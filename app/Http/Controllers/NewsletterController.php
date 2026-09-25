<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['email'=>['required','email:rfc','max:190']]);
        Newsletter::firstOrCreate(['email'=>mb_strtolower($data['email'])]);
        return back()->with('newsletter_subscribed', t('Thank you for subscribing. Keep an eye on your inbox.'));
    }
}
