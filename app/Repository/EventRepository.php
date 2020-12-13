<?php


namespace App\Repository;


use App\Events;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventRepository
{

    /**
     * @param $request
     * @return Events
     */
    public function createEvent($request)
    {
        /*Generate Random Promo Code and Add it to request params*/
        $randomPromoCode = substr(md5(microtime()), 0, 8);
        $request->request->add(['promo_code' => $randomPromoCode]);
        $events = new Events();
        $events->fill($request->all());
        $events->save();
        return $events;
    }

    /**
     * @param $eventId
     * @param $request
     * @return mixed
     */
    public function updateEvent($eventId, $request)
    {
        /*Update the current datetime in Updated at column*/
        $request->request->add(['updated_at' => \Config::get('constants.options.CURRENT_DATE_FORMAT')]);
        $updateEvent = Events::find($eventId);
        $updateEvent->fill($request->all());
        return $updateEvent;
    }

    /**
     * @return mixed
     */
    public function getAllPromoCodes()
    {
        return Events::get()->pluck('promo_code');
    }

    /**
     * @return mixed
     */
    public function getActivePromoCodes()
    {
        return Events::where('is_active', \Config::get('constants.options.IS_ACTIVE'))->get()->pluck('promo_code');
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPromoCode($request)
    {
        /*Get Event by Valid Promo code*/
        $getEventByPromoCode = Events::where('promo_code', $request['promo_code'])->first();
        $getPromoRadiusDistance = $getEventByPromoCode->promo_radius;

        /*Check if Promo code date is expired or not*/
        if ($getEventByPromoCode->promo_expiry < \Config::get('constants.options.CURRENT_DATE_FORMAT')) {
            return response()->json(['error' => 'Promo code expired']);
        }

        /*Check Pickup Location is Under the Event Radius*/
        $checkLocationWithinRadius = $this->scopeIsWithinMaxDistance(
            $request['pickup_point_lat'],
            $request['pickup_point_long'],
            $getPromoRadiusDistance
        );

        if (empty($checkLocationWithinRadius)) {
            /*If Pickup point is not under the event radius check Drop point*/
            $checkLocationWithinRadius = $this->scopeIsWithinMaxDistance(
                $request['drop_point_lat'],
                $request['drop_point_long'],
                $getPromoRadiusDistance
            );
        }

        /*If Pickup or Drop Location is under event radius return Event details else throw error*/
        return !empty($checkLocationWithinRadius) ? $checkLocationWithinRadius : response()->json(['error' => 'Your Pickup or Drop not under event radius']);;
    }

    /**
     * @param $lat
     * @param $long
     * @param $radius
     * @return mixed
     */
    public function scopeIsWithinMaxDistance($lat, $long, $radius)
    {
        /*Using Haversine Concept here to find the nearest location of the event*/
        $haversine = "(3959 * acos(cos(radians($lat)) 
                     * cos(radians(event_lat)) 
                     * cos(radians(event_long) 
                     - radians($long)) 
                     + sin(radians($lat)) 
                     * sin(radians(event_lat))))";
        return Events::select('*')
            ->selectRaw("{$haversine} AS distance")
            ->whereRaw("{$haversine} < ?", [$radius])->first();
    }
}
