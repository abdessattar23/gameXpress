<?php


namespace App\Http\Controllers\Api\V1\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {


        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $fields['password'] = Hash::make($fields['password']);


        $user = User::create($fields);

        if ($user->id == 1) {
            $user->assignRole('super_admin');
        }else {
            $user->assignRole('guest');
        }

        $token = $user->createToken('register token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'role' => $user->getRoleNames(),
            'token' => $token,
            'message' => 'User registred successfully'
        ], 201);
    }

    public function login(Request $request){

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' =>'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Invalid credentials',
                'status' => 'error'
            ], 401);
        }

        // revoke old ones
        $user->tokens()->delete();

        $token = $user->createToken('login token')->plainTextToken;

        return response()->json([
            "user" => $user,
            "token" =>  $token,
            'message' => 'Logged in successfully'
        ], 200);

    }

    public function logout(Request $request)
    {
        try {

            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Successfully logged out',
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during logout',
                'status' => 'error'
            ], 500);
        }
    }
}
