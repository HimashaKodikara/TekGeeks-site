<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class DeclarantFormPage extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'declarant_form_pages';
    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'page_name_en',
        'page_name_si',
        'page_name_ta',
        'page_route',
        'order',
        'status',
        'is_delete',
    ];

    /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('declarant_form_pages')
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName) =>
                "Declarant form page record was {$eventName}"
            );
    }

    /**
     * Boot method to add extra activity properties
     */
    protected static function booted()
    {
        parent::booted();

        Activity::saving(function (Activity $activity) {
            // Read client IP and full URL
            $clientIp = request()->ip();
            $url = request()->fullUrl();

            // Add to activity properties
            $props = collect($activity->properties ?? []);
            // $activity->properties = $props->merge([
            //     'ip'  => $clientIp,
            //     'url' => $url,
            // ]);

            // Also store directly in activity table columns if they exist
            $activity->ip = $clientIp;
            $activity->url = $url;
        });
    }

}
