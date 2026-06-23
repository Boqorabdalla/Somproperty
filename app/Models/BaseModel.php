<?php

namespace App\Models;

use Carbon\Carbon;
use Froiden\RestAPI\ApiModel;

/**
 * App\Models\BaseModel
 *
 * @property-read mixed $icon
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel query()
 *
 * @mixin \Eloquent
 */
class BaseModel extends ApiModel
{
    // It will be override
    protected $dates = [];

    public function asDateTime($value)
    {
        // If the value is already a Carbon instance, we don't need to do anything
        if ($value instanceof Carbon) {
            return $value;
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        // Parse ISO 8061 date
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})\\+(\d{2}):(\d{2})$/', $value)) {
            return Carbon::createFromFormat('Y-m-d\TH:i:sP', $value);
        } elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2}T(\d{2}):(\d{2}):(\d{2})\\.(\d{1,3})Z)$/', $value)) {
            return Carbon::createFromFormat('Y-m-d\TH:i:s.uZ', $value);
        }

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Carbon object
        // that is returned back out to the developers after we convert it here.
        return Carbon::createFromFormat($this->getDateFormat(), $value);
    }

    public static function options($items, $group = null, $columnName = null): string
    {
        $options = '<option value="">--</option>';

        foreach ($items as $item) {

            $name = is_null($columnName) ? $item->name : $item->{$columnName};

            $selected = (! is_null($group) && ($item->id == $group->id)) ? 'selected' : '';

            $options .= '<option '.$selected.' value="'.$item->id.'"> '.($name).' </option>';
        }

        return $options;
    }

    public static function clickAbleLink($route, $title, $other = null)
    {
        return '<div class="media align-items-center">
                        <div class="media-body">
                    <h5 class="mb-0 f-13 text-darkest-grey"><a href="'.$route.'" class="openRightModal">'.$title.'</a></h5>
                    <p class="mb-0">'.$other.'</p>
                    </div>
                  </div>';
    }

    // Added this for $dates
    public function getDates()
    {
        if (! $this->usesTimestamps()) {
            return $this->dates;
        }

        $defaults = [
            $this->getCreatedAtColumn(),
            $this->getUpdatedAtColumn(),
        ];

        return array_unique(array_merge($this->dates, $defaults));
    }
}
