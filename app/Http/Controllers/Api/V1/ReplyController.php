<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Comment;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function replyToTicket(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $ticket = Ticket::find($ticketId);
        $user = auth()->user();
        dd($user);
        $ticket->create([
            'message' => $request->message,
            'user_id' => $user->id,
        ]);


        return response()->json(['message' => 'پاسخ به تیکت با موفقیت ثبت شد.']);
    }

    public function replyToComment(Request $request, $commentId)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $comment = Comment::findOrFail($commentId);

        $comment->replies()->create([
            'message' => $request->message,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'پاسخ به کامنت با موفقیت ثبت شد.']);
    }
}
