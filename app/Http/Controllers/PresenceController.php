<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class PresenceController extends Controller
{
    public function ping(): Response
    {
        return response()->noContent();
    }
}
