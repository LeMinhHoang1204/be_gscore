<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasUuid;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    public function userSubject()
    {
        return $this->belongsToMany(User::class, 'users_subjects', 'subject_id', 'user_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'groups_subjects', 'subject_id', 'group_id');
    }
}
