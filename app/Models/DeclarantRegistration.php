<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class DeclarantRegistration extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'surname',
        'other_names',
        'nationality_id',
        'nic',
        'country_code',
        'mobile_no',
        'mobile_otp',
        'mobile_otp_verification',
        'email',
        'email_otp',
        'email_otp_verification',
        'attention_notice_status',
        'designation_id',
        'institute_id',
        'status'
    ];

     /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('physical_assets_lands_buildings')
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName) =>
                "Declarant registration record {$eventName}"
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

    public function personalInfo()
    {
        return $this->hasOne(DeclarantPersonalInfo::class, 'declarant_registration_id');
    }

    public function logs()
    {
        return $this->hasMany(CommonLog::class, 'user_id');
    }

    public function publicAuthorities()
    {

        return $this->belongsTo(PublicAuthority::class, 'institute_id', 'id')
                    ->where('status', 'Y')
                    ->where('is_delete', 0);
    }

    public function designation()
    {
        // This tells Laravel that designation_id on this table 
        // matches the id on the designations table
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

// App\Models\DeclarantRegistration.php

public function currentDeclarationStatus()
{
    $currentYear = date('Y');

    return $this->hasOne(StatusOfDeclaration::class, 'declarant_registration_id', 'id')
                ->whereYear('created_at', $currentYear)
                ->where('is_delete', 0);
}

public function declarationStatuses()
{
    return $this->hasMany(StatusOfDeclaration::class, 'declarant_registration_id', 'id')
                ->where('is_delete', 0)
                ->with('declarationType');
}

}
