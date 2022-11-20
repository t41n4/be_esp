<?php

namespace App\Http\Controllers;

use App\Models\devices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\devices  $devices
     * @return \Illuminate\Http\Response
     */
    public function show(devices $devices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\devices  $devices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, devices $devices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\devices  $devices
     * @return \Illuminate\Http\Response
     */
    public function destroy(devices $devices)
    {
        //
    }
    // post log
    public function postLog(Request $request)
    {
        try {
            // get token
            $token = $request->bearerToken();
            // bcrypt token
            $token = hash('sha256', $token);
            // get userID by token
            $userID = DB::table('personal_access_tokens')->where('token', $token)->first()->tokenable_id;
            // check if user is admin
            $status = $request->status;

            $status == 'on' ?  DB::table('devices')->where('user_id', $userID)->update(['on_time' => date('Y-m-d H:i:s')]) : null;
            $status == 'off' ? DB::table('devices')->where('user_id', $userID)->update(['off_time' => date('Y-m-d H:i:s')]) : null;

            $db_on_time = DB::table('devices')->where('user_id', $userID)->first()->on_time;
            $db_off_time = DB::table('devices')->where('user_id', $userID)->first()->off_time;

            $duration_time_calc = strtotime($db_off_time) - strtotime($db_on_time);

            // update duration_active_time
            if ($db_off_time > $db_on_time) {
                DB::table('devices')->where('user_id', $userID)->update(['duration_active_time' => $duration_time_calc]);
            } else {
                DB::table('devices')->where('user_id', $userID)->update(['duration_active_time' => 0]);
            }

            // get duration_active_time
            $db_duration_active_time = DB::table('devices')
                ->where('user_id', $userID)
                ->first()
                ->duration_active_time;

            return response()->json([
                'message' => 'success',
                'duration_active_time' => $db_duration_active_time,
                'on_time' => $db_on_time,
                'off_time' => $db_off_time,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'fail',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
