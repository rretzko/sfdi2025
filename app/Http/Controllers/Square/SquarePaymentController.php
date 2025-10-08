<?php

namespace App\Http\Controllers\Square;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SquarePaymentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $candidateId, float $amountDue)
    {
        return view('square.square', compact('candidateId', 'amountDue'));
    }
}
