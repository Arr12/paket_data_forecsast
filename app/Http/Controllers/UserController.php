<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Roles;
use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return false;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = User::pluck('name', 'name')->all();
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }
        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ], 'data' => $data], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data_array = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ];
        $data = User::create($data_array);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }

        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ]], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = User::with('roles')->where('status', '=', 'active')->orderBy('created_at', 'desc')->get();
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $data_array['property'] = [];
        $total = 0;
        $title = [
            "No.",
            "Name",
            "Email",
            "Roles",
            "Opsi"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach ($data as $key => $value) {
            $role_id = $value->roles?->id;
            $btn = "
                <button class='btn btn-danger' type='button' id='btn_delete' data-id='$value->id'><i class='material-icons'>delete</i></button>
                <button class='btn btn-primary' type='button' id='btn_edit' data-id='$value->id' data-name='$value->name' data-email='$value->email' data-role_id='$role_id'><i class='material-icons'>edit</i></button>
            ";
            array_push($data_array['data'], [
                $key + 1,
                $value->name,
                $value->email,
                $value->roles?->name,
                $btn
            ]);
        }
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }
        return response()->json([
            'meta' => [
                'status' => 'success',
                'message' => null
            ],
            'data' => $data,
            'data_array' => $data_array
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = User::where(['id', '=', $id])->get();
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }
        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ], 'data' => $data], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ];
        $data = User::where('id', '=', $request->id);
        $data->update($input);
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }
        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ], 'data' => $data], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // $input = [
        //     'status' => 'deleted'
        // ];
        // $data = User::where('id', $request->id)->update($input);
        $data = User::where('id', $request->id)->delete();
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }
        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ]], 200);
    }
}
