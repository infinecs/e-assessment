<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

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
        // Allow JSON AJAX requests to be parsed as form data
        if ($request->isJson()) {
            $request->merge(json_decode($request->getContent(), true) ?? []);
        }

        Log::info('User creation request:', $request->except(['password', 'password_confirmation']));

        try {
            $validated = $request->validate([
                'email' => [
                    'required',
                    'email:rfc,dns', // Enhanced email validation
                    'unique:login,email',
                    'max:255'
                ],
                'roles' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::in(['admin', 'user']) // Restrict to specific roles
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'regex:/[A-Z]/',           // At least one uppercase letter
                    'regex:/[a-z]/',           // At least one lowercase letter
                    'regex:/[0-9]/',           // At least one number
                    'regex:/[^A-Za-z0-9]/',    // At least one special character
                ],
                'password_confirmation' => 'required|same:password',
            ], [
                'email.unique' => 'This email address is already registered.',
                'roles.in' => 'Please select a valid role.',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            ]);

            // Use database transaction for data integrity
            $user = DB::transaction(function () use ($validated) {
                return User::create([
                    'email' => $validated['email'],
                    'roles' => $validated['roles'],
                    'password' => Hash::make($validated['password'])
                ]);
            });

            Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'roles' => $user->roles,
                    'created_at' => $user->created_at
                ]
            ], 201);

        } catch (ValidationException $e) {
            Log::warning('User creation validation failed', [
                'errors' => $e->errors(),
                'email' => $request->input('email')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('User creation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->input('email')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the user. Please try again.'
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

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('User not found for viewing', ['user_id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error retrieving user', [
                'user_id' => $id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the user'
            ], 500);
        }
    }

    /**
     * Update a specific user (AJAX)
     */
    public function update(Request $request, $id)
    {
        // Allow JSON AJAX requests to be parsed as form data
        if ($request->isJson()) {
            $request->merge(json_decode($request->getContent(), true) ?? []);
        }

        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'email' => [
                    'required',
                    'email:rfc,dns',
                    'max:255',
                    Rule::unique('login', 'email')->ignore($id)
                ],
                'roles' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::in(['admin', 'user'])
                ],
            ], [
                'email.unique' => 'This email address is already registered.',
                'roles.in' => 'Please select a valid role.',
            ]);

            // Use transaction for consistency
            $user = DB::transaction(function () use ($user, $validated) {
                $user->update([
                    'email' => $validated['email'],
                    'roles' => $validated['roles'],
                ]);
                return $user->fresh(); // Get updated model
            });

            Log::info('User updated successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'roles' => $user->roles,
                    'updated_at' => $user->updated_at
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('User not found for update', ['user_id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);

        } catch (ValidationException $e) {
            Log::warning('User update validation failed', [
                'user_id' => $id,
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('User update error', [
                'user_id' => $id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the user'
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
            
            // Prevent self-deletion if needed
            if (auth()->check() && auth()->id() == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ], 403);
            }

            $userEmail = $user->email; // Store for logging
            
            DB::transaction(function () use ($user) {
                $user->delete();
            });

            Log::info('User deleted successfully', [
                'user_id' => $id,
                'email' => $userEmail
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('User not found for deletion', ['user_id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('User deletion error', [
                'user_id' => $id,
                'message' => $e->getMessage()
            ]);

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
            $validated = $request->validate([
                'query' => 'nullable|string|max:255'
            ]);

            $query = $validated['query'] ?? '';
            

            $currentUserEmail = auth()->check() ? auth()->user()->email : null;

            if (empty(trim($query))) {
                $usersQuery = User::orderBy('created_at', 'desc');
                if ($currentUserEmail) {
                    $usersQuery->where('email', '!=', $currentUserEmail);
                }
                return response()->json([
                    'success' => true,
                    'users' => $usersQuery->paginate(20)
                ]);
            }

            $usersQuery = User::where('email', 'LIKE', '%' . trim($query) . '%')
                ->orderBy('created_at', 'desc');
            if ($currentUserEmail) {
                $usersQuery->where('email', '!=', $currentUserEmail);
            }
            $users = $usersQuery->paginate(20);

            Log::info('User search performed', [
                'query' => $query,
                'results_count' => $users->count()
            ]);

            return response()->json([
                'success' => true,
                'users' => $users
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid search query',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('User search error', [
                'query' => $request->input('query'),
                'message' => $e->getMessage()
            ]);

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
                'ids' => 'required|array|min:1|max:100', // Limit bulk operations
                'ids.*' => 'integer|exists:login,id',
            ]);

            $ids = $validated['ids'];
            
            // Prevent self-deletion in bulk operations
            if (auth()->check()) {
                $currentUserId = auth()->id();
                if (in_array($currentUserId, $ids)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot delete your own account'
                    ], 403);
                }
            }

            $deletedCount = DB::transaction(function () use ($ids) {
                return User::whereIn('id', $ids)->delete();
            });

            Log::info('Bulk user deletion completed', [
                'requested_count' => count($ids),
                'deleted_count' => $deletedCount,
                'user_ids' => $ids
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} user(s)!"
            ]);

        } catch (ValidationException $e) {
            Log::warning('Bulk deletion validation failed', [
                'errors' => $e->errors(),
                'ids' => $request->input('ids')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid user IDs provided',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Bulk user deletion error', [
                'ids' => $request->input('ids'),
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting users'
            ], 500);
        }
    }

    /**
     * Get user statistics (optional additional endpoint)
     */
    public function stats()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'admin_users' => User::where('roles', 'admin')->count(),
                'regular_users' => User::where('roles', 'user')->count(),
                'recent_users' => User::where('created_at', '>=', now()->subDays(7))->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving user statistics', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics'
            ], 500);
        }
    }
}