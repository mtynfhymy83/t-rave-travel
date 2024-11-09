<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    // استفاده از سرویس TicketService
    protected $ticketService;

    // سازنده برای تزریق سرویس
    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    // ایجاد تیکت جدید
    public function create(Request $request)
    {
        $ticket = $this->ticketService->createTicket($request);

        if ($ticket) {
            return response()->json($ticket, 201);
        }

        return response()->json([
            'result' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    // دریافت تمامی تیکت‌ها
    public function getAll()
    {
        $userId = auth()->id();
        $tickets = $this->ticketService->getAllTickets($userId);
        return TicketResource::collection($tickets);
    }

    // دریافت جزئیات یک تیکت
    public function getticket($id)
    {
        $ticket = $this->ticketService->getTicketDetails($id);

        return response()->json([
            'body' => $ticket->body,
            'ticketID' => $ticket->id,
            'isAnswer' => $ticket->isAnswer,
            'created_at' => $ticket->created_at,
            'title' => $ticket->title,
        ]);
    }

    // ارسال پاسخ به یک تیکت
    public function setAnswer(Request $request)
    {
        $answer = $this->ticketService->setTicketAnswer($request);

        return response()->json($answer);
    }
}
