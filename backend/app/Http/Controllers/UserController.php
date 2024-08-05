<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['educations', 'cities'])->get();
        return response()->json($users);
    }

    public function updateEducation(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'degree' => 'required|string'
        ]);

        $user = User::find($id);
        if ($user) {
            $education = $user->educations()->first();
            if ($education) {
                $education->update(['degree' => $request->input('degree')]);
                return response()->json(['message' => 'Education updated successfully']);
            } else {
                return response()->json(['message' => 'Education record not found'], 404);
            }
        }
        return response()->json(['message' => 'User not found'], 404);
    }
}
