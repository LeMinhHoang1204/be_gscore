<?php

namespace App\Http\Controllers;

use App\Services\UserSubjectService;
use Illuminate\Http\Request;

class UserSubjectController extends Controller
{
    protected $userSubjectService;

    public function __construct(UserSubjectService $userSubjectService)
    {
        $this->userSubjectService = $userSubjectService;
    }

    public function lookup(Request $request)
    {
        try {
            $request->validate([
                'res_num' => 'required|exists:users,registration_number| not_in:0'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $result = $this->userSubjectService->lookup($request->res_num);

        if (!$result) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($result);
    }

    public function report()
    {
        $result = $this->userSubjectService->report();
        return response()->json($result);
    }

    public function getTopStudents(Request $request)
    {
        request()->validate([
            'group' => 'required|string |exists:groups,name'
        ]);

        $result = $this->userSubjectService->getTopStudents($request->group);

        if (!$result) {
            return response()->json(['error' => 'Invalid group'], 400);
        }

        return response()->json($result);
    }
}
