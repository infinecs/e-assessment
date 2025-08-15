<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display the user management page
     */
    public function index()
    {
        $records = User::orderBy('created_at', 'desc')->paginate(20);
        return view('assessment.user', compact('records'));
    }

    /**
     * Store a newly created user (AJAX)
     */
    public function store(Request $request)
    {
        try {
            // Debug: log incoming request data
            \Log::info('Store request data:', $request->all());

            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'roles' => 'required|string|max:255',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'regex:/[^A-Za-z0-9]/',
                ],
                'password_confirmation' => 'required|same:password',
            ]);

            \Log::info('Validated data:', $validated);

            $user = User::create([
                'email' => $validated['email'],
                'roles' => $validated['roles'],
                'password' => \Hash::make($validated['password']),
                'name' => explode('@', $validated['email'])[0],
            ]);

            \Log::info('User created:', $user->toArray());


            \Log::info('User creation response:', [
                'success' => true,
                'message' => 'User created successfully!',
                'user' => $user
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'user' => $user
            ]);

        } catch (ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Store error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific user for editing (AJAX)
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'roles' => $user->roles,
                    'created_at' => $user->created_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Update a specific user (AJAX)
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $validated = $request->validate([
                'email' => 'required|email|unique:users,email,' . $id,
                'roles' => 'required|string|max:255',
            ]);


            $user->update([
                'email' => $validated['email'],
                'roles' => $validated['roles'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
                'user' => $user
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all()),
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific user (AJAX)
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the user'
            ], 500);
        }
    }

    /**
     * Search users by email (AJAX)
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('query', '');
            
            $users = User::where('email', 'LIKE', '%' . $query . '%')
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

            return response()->json([
                'success' => true,
                'users' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed'
            ], 500);
        }
    }

    /**
     * Bulk delete users by IDs (AJAX)
     */
    public function bulkDestroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:users,id',
            ]);

            $ids = $validated['ids'];
            $deletedCount = User::whereIn('id', $ids)->delete();

            $userText = $deletedCount === 1 ? 'user' : 'users';

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} {$userText}!"
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user IDs provided',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting users'
            ], 500);
        }
    }
}