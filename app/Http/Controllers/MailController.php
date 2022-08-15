<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMailRequest;
use App\Services\MailService;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function __construct(protected MailService $mailService)
    {
    }

    public function create(CreateMailRequest $request)
    {
        $validated = $request->validated();
        $createdMails = $this->mailService->create($validated);
        return response()->json($createdMails, 200);
    }

}