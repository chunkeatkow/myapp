<?php

namespace App\Http\Controllers;

use App\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    public function register(Request $request)
    {
        $inputs = $request->all();

        $validator = Validator::make($inputs, [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'mobile_no' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ["rst" => "0", "msg" => $validator->errors()->toJson(), "data" => []];
        }

        DB::beginTransaction();

        try {
            if (!empty(Client::where('mobile_no', $inputs['mobile_no'])->first())) {
                DB::rollBack();
                return ["rst" => "0", "msg" => "mobile_no_has_been_used", "data" => []];
            }

            if (!empty(Client::where('username', $inputs['username'])->first())) {
                DB::rollBack();
                return ["rst" => "0", "msg" => "username_has_been_used", "data" => []];
            }

            $client = Client::create([
                'name' => $inputs['username'],
                'username' => $inputs['username'],
                'email' => $inputs['email'],
                'mobile_no' => $inputs['mobile_no'],
                'password' => Hash::make($inputs['password']),
            ]);

            if (!$client) {
                DB::rollBack();
                return ["rst" => "0", "msg" => "register_failed", "data" => []];
            }

            JWTAuth::factory()->setTTL(60 * 24 * 365);
            if (! $token = JWTAuth::customClaims(['uname' => $inputs['username'], 'rte' => Carbon::now()->addMinutes(45)->timestamp])
                ->attempt([
                    'username' => $inputs['username'],
                    'password' => $inputs['password']
                ])) {

                return ['status' => false, 'msg' => 'invalid_credentials', 'data' => []];
            }

            $data['access_token'] = compact('client', 'token')['token'];
            $data['user'] = compact('client', 'token')['client'];

            DB::commit();

            return ["rst" => "1", "msg" => "register_success", "data" => $data];
        } catch(\Exception $e) {
            DB::rollBack();

            return ["rst" => "0", "msg" => "register_failed", "data" => []];
        }
    }

    public function authenticate(Request $request)
    {
        $inputs = $request->all();

        try {
            \Tymon\JWTAuth\Facades\JWTAuth::factory()->setTTL(60 * 24 * 365);
            if (! $token = \Tymon\JWTAuth\Facades\JWTAuth::customClaims(['uname' => $inputs['username'], 'rte' => Carbon::now()->addMinutes(45)->timestamp])
                ->attempt([
                    'username' => $inputs['username'],
                    'password' => $inputs['password']
                ])) {

                return ['status' => false, 'msg' => 'invalid_credentials', 'data' => []];
            }
        } catch (JWTException $e) {
            return ["rst" => "0", "msg" => "login_failed", "data" => []];
        }

        $data['access_token'] = compact('token')['token'];
        $tokenParts = explode(".", $data['access_token']);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);
        $data['expired_at'] = $jwtPayload->exp;

        return ["rst" => "1", "msg" => "success", "data" => $data];
    }
}
