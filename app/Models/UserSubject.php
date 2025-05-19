<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class UserSubject extends Model
{
    use HasUuid;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users_subjects';

    protected $fillable = [
        'user_id',
        'subject_id',
        'grade'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
}
