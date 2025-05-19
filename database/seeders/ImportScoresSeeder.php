<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\User;
use App\Models\UserSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImportScoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/public/diem_thi_thpt_2024.csv');
        $file = fopen($path, 'r');

        $header = fgetcsv($file); // Lấy dòng đầu tiên: tiêu đề cột
        $subjectNames = array_slice($header, 1, -1); // loại 'sbd' và 'ma_ngoai_ngu'

        // Tạo subject
        foreach ($subjectNames as $name) {
            Subject::firstOrCreate(['name' => ucfirst(str_replace('_', ' ', $name))]);
        }

        $scienceSubjects = ['vat_li', 'hoa_hoc', 'sinh_hoc'];
        $socialSubjects = ['lich_su', 'dia_li', 'gdcd'];

        $count = 0;
        // Duyệt qua từng dòng trong file CSV
        while (($row = fgetcsv($file)) !== false && $count < 1000) {
            $data = array_combine($header, $row);

            $hasScience = false;
            $hasSocial = false;

            foreach ($scienceSubjects as $name) {
                if (isset($data[$name]) && is_numeric($data[$name])) {
                    $hasScience = true;
                    break;
                }
            }

            foreach ($socialSubjects as $name) {
                if (isset($data[$name]) && is_numeric($data[$name])) {
                    $hasSocial = true;
                    break;
                }
            }

            $examType = null;
            if ($hasScience && !$hasSocial) $examType = 'science';
            elseif ($hasSocial && !$hasScience) $examType = 'social';
            else $examType = 'undefined';

            // Tạo user
            $user = User::create([
                'registration_number' => $data['sbd'],
                'password' => bcrypt('123456'),
                'foreign_language' => $data['ma_ngoai_ngu'] ?? null,
                'is_admin' => false,
                'exam_type' => $examType,
            ]);

            // Ghi điểm cho từng môn
            foreach ($subjectNames as $name) {
                $score = $data[$name] ?? null;
                if ($score !== null && is_numeric($score)) {
                    $subject = Subject::where('name', ucfirst(str_replace('_', ' ', $name)))->first();
                    UserSubject::create([
                        'user_id' => $user->id,
                        'subject_id' => $subject->id,
                        'grade' => $score,
                    ]);
                }
            }
            $count++;
        }

        fclose($file);
    }
}
