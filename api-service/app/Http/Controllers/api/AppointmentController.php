<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\ListAppointmentRequest;
use App\Services\AppointmentService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AppointmentController extends Controller
{
    public function create(
        CreateAppointmentRequest $request,
        AppointmentService $service
    ) {
        try {
            $payload = $request->all();
            $payload['student_id'] = $payload['student_id'] ?? Auth::id();
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

    public function list(
        ListAppointmentRequest $request,
        AppointmentService $service
    ) {
        try {
            $payload = $request->all();
            $payload['user_id'] = Auth::id();
            $data = $service->list($payload);
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

    public function update(
        UpdateAppointmentRequest $request,
        AppointmentService $service,
        $appointmentID
    ) {
        try {
            $payload = $request->all();
            $payload['appointment_id'] = $appointmentID;
            $data = $service->update($payload);
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
