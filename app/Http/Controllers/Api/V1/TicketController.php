<?php

namespace App\Http\Controllers\Api\V1;

    use App\Http\Controllers\Controller;
    use App\Models\Ticket;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function show()
    {
        $userId = Auth::id();
//dd($userId);
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tickets = Ticket::where('user_id', $userId)->get();

        return response()->json($tickets);
    }



    public function store(Request $request)
    {

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $userId = Auth::id();
        $ticket = Ticket::create([
            'subject' => $request->subject,
            'message' => $request->message,
            'user_id' => $userId ,
        ]);

        return response()->json($ticket, 201);
    }

    public function update(Request $request, $id)
    {

        $ticket = Ticket::where('id', $id)->where('user_id', Auth::id())->firstOrFail();


        $request->validate([
            'status' => 'required|in:open,in_progress,closed',
        ]);


        $ticket->status = $request->status;
        $ticket->save();

        return response()->json($ticket);
    }

    public function destroy($id)
    {

        $ticket = Ticket::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $ticket->delete();

        return response()->json(null, 204);
    }
}
