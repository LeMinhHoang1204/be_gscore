<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class GroupSubject extends Model
{
    use HasUuid;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $table = 'groups_subjects';

    protected $fillable = [
        'subject_id',
        'group_id'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id' , 'id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
