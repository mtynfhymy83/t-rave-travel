<?php

// app/Services/TicketService.php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketService
{
    // ایجاد تیکت جدید
    public function createTicket(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'priority' => 'integer',
        ]);

        $user = $request->user();

        if ($user) {
            $ticket = Ticket::create([
                'title' => $validatedData['title'],
                'body' => $validatedData['body'],
                'priority' => $validatedData['priority'],
                'user' => $user->id,
                'answer' => 0,
                'isAnswer' => 0,
            ]);

            return $ticket->load('department', 'departmentSub');
        }

        return null;
    }

    // دریافت تمامی تیکت‌ها برای کاربر
    public function getAllTickets($userId)
    {
        return Ticket::where('user', $userId)->get();
    }

    // دریافت جزئیات تیکت بر اساس ID
    public function getTicketDetails($ticketId)
    {
        return Ticket::findOrFail($ticketId);
    }

    // پاسخ به تیکت
    public function setTicketAnswer(Request $request)
    {
        $validatedData = $request->validate([
            'body' => 'required|string',
            'ticketID' => 'required|exists:tickets,id',
        ]);

        $ticket = Ticket::findOrFail($validatedData['ticketID']);

        $answer = Ticket::create([
            'title' => $ticket->title,
            'body' => $validatedData['body'],
            'parent' => $validatedData['ticketID'],
            'priority' => $ticket->priority,
            'user' => $request->user()->id,
            'isAnswer' => 1,
            'answer' => 0,
            'departmentID' => $ticket->departmentID,
            'departmentSubID' => $ticket->departmentSubID,
        ]);

        $ticket->update(['answer' => 1]);

        return $answer;
    }
}

