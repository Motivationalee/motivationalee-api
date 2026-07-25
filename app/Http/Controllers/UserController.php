<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Mail\UserAccountCreatedMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return User::paginate($request->input('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $plainPassword = Str::password(12);

        $user = User::create([
            ...$request->validated(),
            'password' => $plainPassword
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        try {
            Mail::to($user->email)->send(new UserAccountCreatedMail($user, $plainPassword));
        } catch (Throwable $e) {
            Log::error('Failed to send user account created email.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        return $user;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if($user->id === Auth::id()) throw new \Exception('You cannot delete your own account');
        if($user->email === config('app.super_admin_email')) throw new \Exception('You cannot delete the super admin account');

        $user->delete();
        return response()->json([
            'message' => 'User archived successfully'
        ], 200);
    }

    public function archiveList(Request $request) {
        return User::onlyTrashed()->paginate($request->input('per_page', 10));
    }

    public function archiveRestore($id) {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return response()->json([
            'message' => 'User restored successfully'
        ], 200);
    }

    public function archiveDelete($id) {
        $user = User::onlyTrashed()->findOrFail($id);
        if($user->email === config('app.super_admin_email')) throw new \Exception('You cannot delete the super admin account');
        $user->forceDelete();
        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}
