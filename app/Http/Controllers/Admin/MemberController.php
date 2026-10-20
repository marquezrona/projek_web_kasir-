<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\NfcCard;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(): View
    {
        $members = Customer::query()
            ->with(['nfcCard.transactions' => fn ($query) => $query->latest()->limit(1)])
            ->latest()
            ->paginate(25);

        return view('admin.members', [
            'members' => $members,
            'memberCount' => Customer::query()->count(),
            'totalBalance' => NfcCard::query()->sum('balance'),
        ]);
    }
}
