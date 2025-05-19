<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name'
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'groups_subjects', 'group_id', 'subject_id');
    }
}
