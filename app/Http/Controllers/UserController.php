<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'phone' => 'required',
            'password' => ['required', 'min:6']
        ]);
        $user = User::create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password)
            ]
        );

        return UserResource::make($user)->additional([
            'token' => $user->createToken('midtrans')->plainTextToken
        ]);
    }
    public function login(Request $request)
    {

        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6']
        ]);

        

        if (!Auth::attempt($request->only('email', 'password')))
        {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        User::where('email', $request['email']);
        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('midtrans')->plainTextToken;

        return UserResource::make($user)->additional([
            'token' => $token
        ]);
    }

    public function google(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password')))
        {
            $this->store($request);
        }

        User::where('email', $request['email'])->update(['foto' =>$request->foto]);
        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('cook4life')->plainTextToken;

        return UserResource::make($user)->additional([
            'token' => $token
        ]);
    }

    public function fcm(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'fcm' => 'required',
        ]);
        $user->fcm = $request->fcm;
        $user->save();
        return UserResource::make($user)->additional([
            'status' => "sukses"
        ]); 
    }
}
