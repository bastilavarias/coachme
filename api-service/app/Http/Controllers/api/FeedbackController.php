<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\CreateFeedbackRequest;
use App\Http\Requests\Feedback\ListFeedbackRequest;
use App\Services\FeedbackService;
use Exception;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function create(
        CreateFeedbackRequest $request,
        FeedbackService $service
    ) {
        try {
            $payload = $request->all();
            $payload['reviewer_id'] = Auth::id();
            $data = $service->create($payload);
            return customResponse()
                ->data($data)
                ->message('API request done.')
                ->success()
                ->generate();
        } catch (Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }
}
