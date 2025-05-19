<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupSubject;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupAndSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map subject name -> id
        $subjectMap = Subject::all()->pluck('id', 'name')->toArray();

        // Danh sách tổ hợp khối: code => [các môn]
        $groups = [
            'A00' => ['Toan', 'Vat li', 'Hoa hoc'],
            'A01' => ['Toan', 'Vat li', 'Ngoai ngu'],

            'B00' => ['Toan', 'Hoa hoc', 'Sinh hoc'],
            'B08' => ['Toan', 'Sinh hoc', 'Ngoai ngu'],

            'C00' => ['Ngu van', 'Lich su', 'Dia li'],

            'D01' => ['Ngu van', 'Toan', 'Ngoai ngu'],
            'D07' => ['Hoa hoc', 'Toan', 'Ngoai ngu'],
        ];


        foreach ($groups as $code => $subjects) {
            $group = Group::firstOrCreate(['name' => $code]);

            foreach ($subjects as $subjectName) {
                $subjectId = $subjectMap[$subjectName] ?? null;

                if ($subjectId) {
                    GroupSubject::create([
                        'subject_id' => $subjectId,
                        'group_id' => $group->id,
                    ]);
                } else {
                    echo "⚠️ Môn học \"$subjectName\" chưa tồn tại trong bảng subjects\n";
                }
            }
        }
    }
}
