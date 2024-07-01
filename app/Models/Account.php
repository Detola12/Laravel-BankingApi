<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'accountNo',
        'user_id',
        'lastTransactionAt'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
