<?php

namespace App\Http\Controllers;

use App\Events;
use App\Repository\EventRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{

    public $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @return mixed
     */
    public function getAllEvents()
    {
        return Events::get();
    }

    /**
     * @param Request $request
     * @return Events|\Illuminate\Http\JsonResponse
     */
    public function createEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|unique:events',
            'event_lat' => 'required',
            'event_long' => 'required',
            'event_address' => 'required',
            'promo_expiry' => 'required',
            'promo_radius' => 'required',
            'promo_percentage_value' => 'required',
            'is_active' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()]);
        }

        $createEvent = $this->eventRepository->createEvent($request);
        return $createEvent;
    }

    /**
     * @param $eventId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function updateEvent($eventId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promo_expiry' => 'required',
            'promo_radius' => 'required',
            'promo_percentage_value' => 'required',
            'is_active' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $updateEvent = $this->eventRepository->updateEvent($eventId,$request);
        return $updateEvent;
    }

    /**
     * @return mixed
     */
    public function getAllPromoCodes()
    {
        return $this->eventRepository->getAllPromoCodes();
    }

    /**
     * @return mixed
     */
    public function getActivePromoCodes()
    {
        return $this->eventRepository->getActivePromoCodes();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPromoCode(Request $request)
    {
        /*Check Promo code exists in the table or not*/
        $validator = Validator::make($request->all(), [
            'promo_code' => 'required|exists:events',
            'pickup_point_lat' => 'required',
            'pickup_point_long' => 'required',
            'drop_point_lat' => 'required',
            'drop_point_long' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $checkPromoCode = $this->eventRepository->checkPromoCode($request);
        return $checkPromoCode;
    }
}
