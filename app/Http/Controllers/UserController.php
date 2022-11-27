<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Order;
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
    public function saldo(Request $request){
        $order = Order::where('user_id','=',$request->user()->id)->where('status','=','SUCCESS')->get();
        $sum = 0;
        foreach ($order as $item) {
            $sum += $item->total;
        }

        return response(['saldo'=>$sum], 200);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'phone' => 'required',
            'password' => ['required', 'min:6']
        ]);
        if (User::where('email', $request['email'])->first() === null) {
            $user = User::create(
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password)
                ]
            );
            $user['token'] = $user->createToken('midtrans')->plainTextToken;

            return response(array(
                'message' => "Berhasil mendaftar",
                "data" => $user,
                'success' => true
            ), 200);
        }

        return response(array(
            'message' => "Email sudah digunakan",
            "data" => null,
            'token' => null,
            'success' => false
        ), 401);
    }
    public function login(Request $request)
    {

        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6']
        ]);



        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email atau Password Anda salah',
                "data" => null,
                'success' => false
            ], 404);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $user['token'] = $user->createToken('midtrans')->plainTextToken;
        return response(array(
            'message' => "Berhasil login",
            "data" => $user,
            'success' => true
        ), 200);
    }

    public function google(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            $this->store($request);
        }

        User::where('email', $request['email'])->update(['foto' => $request->foto]);
        $user = User::where('email', $request['email'])->firstOrFail();
        $user['token'] = $user->createToken('midtrans')->plainTextToken;
        return response(array(
            'message' => "Berhasil login",
            "data" => $user,
            'success' => true
        ), 200);
    }

    public function fcm(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'fcm' => 'required',
        ]);
        $user->fcm = $request->fcm;
        $user->save();
        return response(array(
            'message' => "Berhasil update fcm",
            'success' => true
        ), 200);
    }
}
