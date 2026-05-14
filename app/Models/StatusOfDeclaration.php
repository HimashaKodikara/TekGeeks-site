<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusOfDeclaration extends Model
{
    public function declarationType()
    {
        return $this->belongsTo(DeclarationType::class, 'declaration_type_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    protected $fillable = [
        'declarant_registration_id',
        'pref_lang',
        'declaration_type_id',
        'purpose_of_declaration_id',
        'declaration_year',
        'completed_date',
        'sequence_number',
        'reference_number',
        'is_recompleted',
        'recompleted_date',
        'pdf_path',
        'pdf_generated_at',
        'status',
        'report_status',
        'comments',
        'is_delete'
    ];
}
