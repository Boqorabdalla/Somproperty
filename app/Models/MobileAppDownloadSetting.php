<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileAppDownloadSetting extends Model
{
    public const DEFAULT_APP_IOS = 'https://apps.apple.com/us/app/worksuite/id1473933050';

    public const DEFAULT_APP_ANDROID = 'https://play.google.com/store/apps/details?id=com.froiden.worksuite';

    protected $guarded = ['id'];

    /**
     * Single global row (no company scope).
     */
    public static function instance(): self
    {
        $row = static::query()->first();

        if ($row !== null) {
            return $row;
        }

        return static::query()->create([
            'app_ios' => self::DEFAULT_APP_IOS,
            'app_android' => self::DEFAULT_APP_ANDROID,
        ]);
    }
}
