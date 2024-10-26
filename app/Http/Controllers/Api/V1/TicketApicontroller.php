<?php


namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\TicketResource;
use App\Http\Resources\UserResource;
use App\Http\services\Keys;
use App\Models\Article;
use App\Models\Department;
use App\Models\DepartmentSub;
use App\Models\Ticket;
use Illuminate\Http\Request;


class TicketApicontroller extends Controller
{
    public function create(Request $request)
    {

        $validatedData = $request->validate([

            'title' => 'required|string',
            'body' => 'required|string',
            'priority' => 'integer',

        ]);
        $user = auth()->user();

        if ($user) {

            $ticket = Ticket::create([

                'title' => $validatedData['title'],
                'body' => $validatedData['body'],
                'priority' => $validatedData['priority'],
                'user' => $user->id,
            'answer' => 0,
            'isAnswer' => 0,

            ]);

            return response()->json($ticket->load('department', 'departmentSub'), 201);
        }
    }

    public function userTickets()
    {
        $tickets = Ticket::where('user', $request->user()->id)
            ->where('isAnswer', 0)
            ->with(['department', 'departmentSub', 'user'])
            ->latest()
            ->get();

        $ticketsArray = $tickets->map(function ($ticket) {
            return [
                ...$ticket->toArray(),
                'departmentID' => $ticket->department->title,
                'departmentSubID' => $ticket->departmentSub->title,
                'user' => $ticket->user->name,
            ];
        });

        return response()->json($ticketsArray);
    }

    public function getAll()
    {
        $user = auth()->id();
        $ticket = Ticket::query()->where('user', $user)->get();
        return TicketResource::collection($ticket);
    }


    public function getticket($id)
    {
        $ticket = Ticket::findOrFail($id);
//        $answerTicket = Ticket::where('parent', $id)->first();

        return response()->json([
            'body' => $ticket->body,
             'ticketID' => $ticket->id,
            'is_Answer' => $ticket->is_Answer,
            'created_at'=>$ticket->created_at,
             'title' => $ticket->title,
//            'answer' => $answerTicket ? $answerTicket->body : null,
        ]);
    }

    public function setAnswer(Request $request)
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

        return response()->json($answer);
    }

    public function departments()
    {
        $departments = Department::all();
        return response()->json($departments);
    }

    public function departmentsSubs($id)
    {
        $departmentSubs = DepartmentSub::where('parent', $id)->get();
        return response()->json($departmentSubs);
    }
}
