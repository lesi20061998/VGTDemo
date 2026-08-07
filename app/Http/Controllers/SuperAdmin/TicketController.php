<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ProjectTicket;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = ProjectTicket::with(['project', 'creator'])
            ->latest()
            ->paginate(20);

        return view('superadmin.tickets.index', compact('tickets'));
    }
}
