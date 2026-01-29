<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealerRequest extends Model
{
    protected $fillable = [
        'full_name',
        'tc_no',
        'email',
        'phone',
        'address',
        'document_pdf_path',
        'document_jpeg_path',
        'status',
        'approved_at',
        'approved_by',
        'created_company_id',
        'created_user_id',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdCompany()
    {
        return $this->belongsTo(Company::class, 'created_company_id');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }
}

