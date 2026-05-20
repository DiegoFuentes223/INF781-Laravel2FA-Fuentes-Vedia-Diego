<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorBackupCode extends Model
{
    protected $fillable = ['user_id', 'code', 'used'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}