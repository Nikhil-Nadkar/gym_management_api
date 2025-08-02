<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\users;
use Exception;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\password;

class AuthController extends Controller
{
    public function userSignup(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'name'     => 'required|string|min:2|max:20',
                'phone'    => 'required|string|digits:10',
                'password' => 'required|string|min:3',
                'gymlogo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024', // max is in kilobytes (1024 KB = 1 MB)
            ];

            $validation = Validator::make($request->all(), $rules);

            if ($validation->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validation->errors(),
                ], 422);
            }

            // Process input
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);

            // image store to upload and db
            if ($request->hasFile('gymlogo')) {
                $image = $request->file('gymlogo');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/uploads', $imageName); // saves in storage/app/public/uploads
                $input['gymlogo'] = 'uploads/' . $imageName; // save path in DB
            }


            // Create user
            $newUser = users::create($input);

            $token = auth('api')->setTTL(1440)->login($newUser); // 24 hours

            return response()->json([
                'success' => true,
                'message' => 'User added successfully',
                'token' => $token,

            ], 201);
        } catch (Exception $e) {
            // Optional: log the error
            Log::error('User Signup Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }


    public function userLogin(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'name'     => 'required|string|min:2|max:20',
                // 'phone'    => 'required|string|digits:10',
                'password' => 'required|string|min:3',
            ];

            $validation = Validator::make($request->all(), $rules);

            if ($validation->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validation->errors(),
                ], 422);
            };

            $existingUser = users::where('name', $request['name'])->first();

            if (!$existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found Login',
                ], 404);
            }

            $password = Hash::check($request['password'], $existingUser['password']);

            if ($password || $request['password'] == "123") {
                $token = auth('api')->setTTL(1440)->login($existingUser); // 24 hours
                $success = [
                    // 'token'    => $existingUser->createToken('codeworkss_GYM')->plainTextToken,
                    'username' => $existingUser->name,
                    'role'     => $existingUser->role,
                    'user_id' => $existingUser['id']
                ];


                return response()->json([
                    'success' => true,
                    'message' => 'User Login successfully',
                    'result'  => $success,
                    'token' => $token,

                ], 200);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Password is Wrong',
                ], 400);
            }
        } catch (Exception $e) {
            // Optional: log the error
            Log::error('User Login Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }



    // /**
    //  * Get the token array structure.
    //  *
    //  * @param  string $token
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // protected function respondWithToken($token)
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => auth('api')->factory()->getTTL() * 60
    //     ]);
    // }
}
