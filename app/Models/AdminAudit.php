<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAudit extends Model
{
    //
    protected $fillable = ['admin_id','action','target_type','target_id','meta'];

protected $casts = [
    'meta' => 'array',
];

}
