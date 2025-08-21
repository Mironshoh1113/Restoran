<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
	public function index()
	{
		$messages = ContactMessage::orderByDesc('created_at')->paginate(20);
		return view('admin.super.contact-messages.index', compact('messages'));
	}

	public function show(ContactMessage $contactMessage)
	{
		if ($contactMessage->status === 'new') {
			$contactMessage->update(['status' => 'read']);
		}
		return view('admin.super.contact-messages.show', compact('contactMessage'));
	}

	public function updateStatus(Request $request, ContactMessage $contactMessage)
	{
		$validated = $request->validate([
			'status' => 'required|in:new,read,responded,archived'
		]);
		$contactMessage->status = $validated['status'];
		if ($validated['status'] === 'responded' && !$contactMessage->responded_at) {
			$contactMessage->responded_at = now();
		}
		$contactMessage->save();
		return back()->with('success', 'Holat yangilandi');
	}

	public function destroy(ContactMessage $contactMessage)
	{
		$contactMessage->delete();
		return redirect()->route('super.contact-messages.index')->with('success', 'Xabar o\'chirildi');
	}
} 