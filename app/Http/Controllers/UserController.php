<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $status_code = 200;

    public function userSignUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => "required",
            "last_name" => "required",
            "email" => "required|email",
            "mobile_number" => "required",
            "password" => "required"
        ]);

        if($validator->fails())
        {
            return response()->json(["status" => "failed", "success" => false, "message" => "validator_error", "errors" => $validator->errors()]);
        }

        $userDataArray = [
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "email" => $request->email,
            "mobile_number" => $request->mobile_number,
            "password" => md5($request->password)
        ];

        $user_status = User::where("email", $request->email)->first();

        if(!is_null($user_status))
        {
            return response()->json(["status"=>"failed", "success" => false, "message" => "Email already registered."]);
        }

        $user = User::create($userDataArray);
        if(!is_null($user))
        {
            return response()->json(["status"=>$this->status_code, "success"=>true, "message"=>"Registration completed successfully", "data" => $user]);
        }
        else {
            return response()->json(["status"=>"failed", "message"=>"Failed to register"]);
        }
    }

    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required",
            "password" => "required|email"
        ]);

        if($validator->fails())
        {
            return response()->json(["status"=>"failed", "validation_error"=>$validator->errors()]);
        }

        $email_status = User::where("email", $request->email)->first();

        if(!is_null($email_status))
        {
            $password_status = User::where("email", $request->email)
            ->where("password", md5($request->password))
            ->first();
            if(!is_null($password_status)){
                $user = $this->userDetail($request->email);

                return response()->json(["status"=>$this->status_code,
                "status"=>true,
                "message"=>"You have logged in successfully",
                "data"=>$user]);
            }
            else{
                return response()->json(["status"=>"failed", "success"=>"false", "message"=>"Unable to login. Incorrect password."]);
        
            }
        }
        else{
            return response()->json(["status"=>"failed", "success"=>"false", "message"=>"Unable to login. Email doesn't exist."]);
        }
    }

    public function userData($email){
        $user = [];
        if(!empty($email))
        {
            $user = User::where("enail", $email)->first();
        }
        return $user;
    }

}
