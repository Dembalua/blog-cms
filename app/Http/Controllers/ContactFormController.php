<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ContactFormController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'cf-turnstile-response' => ['required', 'string'],
        ]);

        $verified = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $data['cf-turnstile-response'],
            'remoteip' => $request->ip(),
        ])->json('success', false);

        if (! $verified) {
            return response()->json(['message' => __('Security check failed. Please try again.')], 422);
        }

        Mail::to(config('app.contact_mail_to'))->send(new ContactFormMessage(
            senderName: $data['name'] ?? '',
            senderEmail: $data['email'],
            senderMessage: $data['message'],
        ));

        return response()->json(['message' => 'OK']);
    }
}
