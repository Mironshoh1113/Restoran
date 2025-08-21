<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
	public function submit(Request $request)
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|max:255',
			'message' => 'required|string|max:5000',
		]);

		ContactMessage::create([
			'name' => $validated['name'],
			'email' => $validated['email'],
			'message' => $validated['message'],
			'ip_address' => $request->ip(),
			'user_agent' => $request->userAgent(),
		]);

		return back()->with('status', "Xabaringiz yuborildi! Tez orada bog'lanamiz.");
	}
} 