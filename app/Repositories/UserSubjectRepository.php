<?php

namespace App\Repositories;

use App\Models\UserSubject;
use Illuminate\Support\Facades\DB;

class UserSubjectRepository
{
    public function getStatsBySubjects($subjectKeys)
    {
        return UserSubject::selectRaw('SUM(CASE WHEN grade < 4 THEN 1 ELSE 0 END) as level_below_4')
            ->selectRaw('SUM(CASE WHEN grade >= 4 AND grade < 6 THEN 1 ELSE 0 END) as level_4_6')
            ->selectRaw('SUM(CASE WHEN grade >= 6 AND grade < 8 THEN 1 ELSE 0 END) as level_6_8')
            ->selectRaw('SUM(CASE WHEN grade >= 8 THEN 1 ELSE 0 END) as level_8plus')
            ->join('subjects', 'users_subjects.subject_id', '=', 'subjects.id')
            ->whereIn('subjects.name', $subjectKeys)
            ->first();
    }

    public function getStatsBySubject()
    {
        return UserSubject::selectRaw('subjects.name as subject')
            ->selectRaw('SUM(CASE WHEN grade < 4 THEN 1 ELSE 0 END) as level_below_4')
            ->selectRaw('SUM(CASE WHEN grade >= 4 AND grade < 6 THEN 1 ELSE 0 END) as level_4_6')
            ->selectRaw('SUM(CASE WHEN grade >= 6 AND grade < 8 THEN 1 ELSE 0 END) as level_6_8')
            ->selectRaw('SUM(CASE WHEN grade >= 8 THEN 1 ELSE 0 END) as level_8plus')
            ->join('subjects', 'users_subjects.subject_id', '=', 'subjects.id')
            ->groupBy('subjects.name')
            ->get();
    }

    public function getTopStudentsBySubjectIds($subjectIds, $limit = 10)
    {
        return DB::table('users_subjects')
            ->join('users', 'users_subjects.user_id', '=', 'users.id')
            ->whereIn('users_subjects.subject_id', $subjectIds)
            ->select(
                'users.name',
                'users.registration_number',
                DB::raw('SUM(users_subjects.grade) as total_score')
            )
            ->groupBy('users.id', 'users.name', 'users.registration_number')
            ->orderByDesc('total_score')
            ->limit($limit)
            ->get();
    }
}
