<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageService
{
    /**
     * ارسال پیام با فایل ضمیمه
     */
    public function sendMessageWithAttachment(Request $request)
    {
        // اعتبارسنجی ورودی
        $validated = $request->validate([
            'message' => 'required|string',
            'ticket_id' => 'required|exists:tickets,id',
            'upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,zip',
        ]);

        // شناسایی کاربر فعلی
        $user = auth()->user();

        $filePath = null;

        // بارگذاری فایل اگر موجود باشد
        if ($request->hasFile('upload')) {
            // ذخیره فایل در سیستم
            $filePath = $this->uploadFile($request->file('upload'));
        }

        // ذخیره پیام در پایگاه داده
        $message = Message::saveMessageWithFile([
            'sender_id' => $user->id,
            'message' => $validated['message'],
            'ticket_id' => $validated['ticket_id']
        ], $filePath);

        return $message;
    }

    /**
     * مدیریت بارگذاری فایل
     */
    private function uploadFile($file)
    {
        // ذخیره فایل در پوشه messages
        $filePath = $file->store('messages', 'public');

        // در صورت نیاز به فضای ابری (مثل S3)، این قسمت را تغییر دهید:
        // $filePath = $file->store('messages', 's3');

        return $filePath;
    }

    /**
     * دریافت پیام‌ها برای تیکت
     */
    public function getMessagesForTicket($ticketId)
    {
        $messages = Message::where('ticket_id', $ticketId)->get();

        foreach ($messages as $message) {
            if ($message->file_path) {
                // اضافه کردن URL فایل به پیام‌ها
                $message->file_url = url('storage/' . $message->file_path);
            }
        }

        return $messages;
    }

}
