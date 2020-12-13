<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $fillable = ['event_name','event_address','event_lat','event_long','promo_code','promo_expiry','promo_radius','promo_percentage_value','is_active','updated_at'];
}
