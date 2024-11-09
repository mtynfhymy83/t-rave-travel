<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * ارسال پیام با فایل ضمیمه
     */
    public function sendMessageWithAttachment(Request $request)
    {
        try {
            $message = $this->messageService->sendMessageWithAttachment($request);

            return response()->json([
                'result' => true,
                'message' => 'Message sent successfully!',
                'data' => $message
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to send message. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * دریافت پیام‌ها برای یک تیکت خاص
     */
    public function getMessagesForTicket($ticketId)
    {
        try {
            $messages = $this->messageService->getMessagesForTicket($ticketId);

            return response()->json([
                'result' => true,
                'message' => 'Messages retrieved successfully!',
                'data' => $messages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to retrieve messages. ' . $e->getMessage(),
            ], 500);
        }
    }
}

