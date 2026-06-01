<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUserMail;
use App\Mail\ContactAdminMail;

class ContactController extends Controller
{
    public function show()
    {
        return view('static.contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'honeypot' => 'present|size:0', // Anti-spam honeypot
        ], [
            'honeypot.size' => 'Bot detected!',
        ]);

        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Send Email to User
        try {
            Mail::to($request->email)->send(new ContactUserMail($contact));
        } catch (\Exception $e) {
            // Log error or ignore
        }

        // Send Email to Admins
        $adminEmails = ['smartestdevelopers@gmail.com', 'nosheeniftikhar00@gmail.com'];
        try {
            Mail::to($adminEmails)->send(new ContactAdminMail($contact));
        } catch (\Exception $e) {
            // Log error or ignore
        }

        return back()->with('success', 'Thanks! Our representatives will contact you shortly.');
    }
}
