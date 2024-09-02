<?php

namespace App\ValueObjects;

use App\Models\Address;
use App\Models\Geostate;
use App\Models\Schools\School;

class AddressValueObject
{
    public static function getStringVo(Address $address): string
    {
        //early exit
        if(! $address->id){ return ''; }

        $str = $address->address1;
        $str .= (strlen($address->address2)) ? ', ' . $address->address2 : '';
        $str .= (strlen($address->city)) ? ', ' . $address->city : '';
        $str .= (strlen($address->geostate_id)) ? '  ' . Geostate::find($address->geostate_id)->abbr : '';
        $str .= (strlen($address->postal_code)) ? '  ' . $address->postal_code : '';

        return $str;
    }
}
