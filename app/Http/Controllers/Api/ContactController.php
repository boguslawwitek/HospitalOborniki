<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        $contact = Contact::first();
        
        if (!$contact) {
            return response()->json([
                'message' => 'Nie znaleziono danych kontaktowych',
            ], 404);
        }
        
        return response()->json([
            'contact' => $contact,
        ]);
    }
    
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'agreement' => 'required|boolean',
        ]);
        
        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Błąd walidacji',
                    'errors' => $validator->errors(),
                ], 422);
            }
            
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $contact = Contact::first();
        $recipientEmail = $contact ? $contact->system_email : config('mail.from.address');
        
        try {
            Mail::to($recipientEmail)->send(new \App\Mail\ContactFormMail(
                $request->name,
                $request->email,
                $request->subject,
                $request->message,
                $request->phone ?? null
            ));
            
            $successMessage = 'Wiadomość została wysłana pomyślnie';
            
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $successMessage,
                ], 200);
            }
            
            return redirect()->route('contact')->with('success', $successMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Wystąpił błąd podczas wysyłania wiadomości';
            
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $errorMessage,
                    'error' => $e->getMessage(),
                ], 500);
            }
            
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }
}
