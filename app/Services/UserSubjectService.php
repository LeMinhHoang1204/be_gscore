<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Subject;
use App\Models\User;
use App\Models\UserSubject;
use App\Repositories\UserSubjectRepository;
use Illuminate\Support\Facades\DB;

class UserSubjectService
{
    protected $userSubjectRepo;

    public function __construct(UserSubjectRepository $userSubjectRepo,) {
        $this->userSubjectRepo = $userSubjectRepo;
    }

    public function lookup($res_num)
    {
        $user = User::where('registration_number', $res_num)->first();

        if (!$user) {
            return null;
        }

        $userSubjects = UserSubject::where('user_id', $user->id)->with('subject')->get();

        $subjectMap = $userSubjects->mapWithKeys(function ($item) {
            return [$item->subject->name => $item->grade];
        });

        $coreSubjects = [
            'Toan' => 'Toán',
            'Ngu van' => 'Ngữ văn',
            'Ngoai ngu' => 'Ngoại ngữ',
        ];

        $science = [
            'Vat li' => 'Vật lí',
            'Hoa hoc' => 'Hóa học',
            'Sinh hoc' => 'Sinh học',
        ];

        $social = [
            'Lich su' => 'Lịch sử',
            'Dia li' => 'Địa lí',
            'Gdcd' => 'Giáo dục công dân',
        ];

        $extraSubjects = match ($user->exam_type) {
            'science' => $science,
            'social' => $social,
            default => array_merge($science, $social),
        };

        $allSubjects = array_merge($coreSubjects, $extraSubjects);

        $resultSubjects = collect($allSubjects)->map(function ($label, $key) use ($subjectMap) {
            return [
                'subject' => $label,
                'grade' => $subjectMap[$key] ?? 'Không thi',
            ];
        })->values();

        $groups = Group::with('subjects')->orderBy('name')->get()->mapWithKeys(function ($group) {
            return [
                $group->name => $group->subjects->pluck('name')->toArray()
            ];
        });

        $group_scores = $groups->map(function ($subjects, $groupName) use ($subjectMap) {
            $total = 0;
            foreach ($subjects as $subject) {
                $total += $subjectMap[$subject] ?? 0;
            }
            return [
                'group_name' => $groupName,
                'total_score' => round($total, 2),
            ];
        })->values();

        return [
            'status' => 'success',
            'subjects' => $resultSubjects,
            'groups_scores' => $group_scores,
        ];
    }

    public function report()
    {
        $groups = Group::with('subjects')->orderBy('name')->get()->mapWithKeys(function ($group) {
            return [
                $group->name => $group->subjects->pluck('name')->toArray()
            ];
        });

        $nameSubjects = [
            'Toan' => 'Toán',
            'Ngu van' => 'Ngữ văn',
            'Ngoai ngu' => 'Ngoại ngữ',
            'Vat li' => 'Vật lí',
            'Hoa hoc' => 'Hóa học',
            'Sinh hoc' => 'Sinh học',
            'Lich su' => 'Lịch sử',
            'Dia li' => 'Địa lí',
            'Gdcd' => 'Giáo dục công dân',
        ];

        $by_groups = [];
        $raw = $this->userSubjectRepo->getStatsBySubject();
        foreach ($groups as $groupCode => $subjectKeys) {
            $subjectLabels = array_map(fn($key) => $nameSubjects[$key], $subjectKeys);

            $nameSubjectsFilter = array_filter(
                $nameSubjects,
                fn($_, $key) => in_array($key, $subjectKeys),
                ARRAY_FILTER_USE_BOTH
            );

            $stats = $this->userSubjectRepo->getStatsBySubjects($subjectKeys);

            $bySubjectsInGroups = collect($nameSubjectsFilter)->map(function ($label, $key) use ($raw) {
                $subjectStat = $raw->firstWhere('subject', $key);
                return [
                    'subject' => $label,
                    'level_below_4' => $subjectStat->level_below_4 ?? 0,
                    'level_4_6' => $subjectStat->level_4_6 ?? 0,
                    'level_6_8' => $subjectStat->level_6_8 ?? 0,
                    'level_8plus' => $subjectStat->level_8plus ?? 0,
                ];
            })->values();

            $by_groups[] = [
                'group' => $groupCode,
                'subjects' => $bySubjectsInGroups,
                'level_below_4' => $stats->level_below_4 ?? 0,
                'level_4_6' => $stats->level_4_6 ?? 0,
                'level_6_8' => $stats->level_6_8 ?? 0,
                'level_8plus' => $stats->level_8plus ?? 0,
            ];
        }

        $bySubjects = collect($nameSubjects)->map(function ($label, $key) use ($raw) {
            $subjectStat = $raw->firstWhere('subject', $key);

            return [
                'subject' => $label,
                'level_below_4' => $subjectStat->level_below_4 ?? 0,
                'level_4_6' => $subjectStat->level_4_6 ?? 0,
                'level_6_8' => $subjectStat->level_6_8 ?? 0,
                'level_8plus' => $subjectStat->level_8plus ?? 0,
            ];
        })->values();

        return [
            'by_groups' => $by_groups,
            'by_subjects' => $bySubjects,
        ];
    }

    public function getTopStudents($group)
    {
        $groups = Group::with('subjects')->orderBy('name')->get()->mapWithKeys(function ($group) {
            return [
                $group->name => $group->subjects->pluck('name')->toArray()
            ];
        });

        $subjectKeys = $groups[$group] ?? null;

        if (!$subjectKeys) {
            return null;
        }

        $subjectIds = Subject::whereIn('name', $subjectKeys)->pluck('id');
        return $this->userSubjectRepo->getTopStudentsBySubjectIds($subjectIds);
    }
}
