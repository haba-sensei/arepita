<?php

namespace App\Http\Livewire\Auth;

use App\Http\Livewire\BaseLivewireComponent;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Propaganistas\LaravelPhone\PhoneNumber;


class RegisterLivewire extends BaseLivewireComponent
{
    public $vendor_name;
    public $vendor_email;
    public $vendor_phone;

    public $name;
    public $phone;
    public $email;
    public $referalCode;
    public $password;

    protected $rules = [
        "email" => "required|email|unique:users",
        "phone" => "required|phone|unique:users",
        "password" => "required|string|min:6",
    ];

    protected $messages = [
        "email.exists" => "Email not associated with any account"
    ];


    public function driverSignUp()
    {

        
        $this->validate(
            [
                "name" => "required",
                "email" => "required|email|unique:users",
                "phone" => "required|phone:" . setting('countryCode', "GH") . "|unique:users",
                "password" => "required|string|min:6",
            ]
        );

        //
        try {

            //
            $phone = PhoneNumber::make($this->phone);
            //
            $user = User::where('phone', $phone)->first();
            if (!empty($user)) {
                throw new Exception("Account with phone already exists", 1);
            }


            DB::beginTransaction();
            //
            $user = new User();
            $user->name = $this->name;
            $user->email = $this->email;
            $user->phone = $phone;
            $user->creator_id = Auth::id();
            $user->commission = 0.00;
            $user->password = Hash::make($this->password);
            $user->is_active = false;
            $user->save();
            //assign role
            $user->assignRole('driver');

            //refer system is enabled
            $enableReferSystem = (bool) setting('enableReferSystem', "0");
            $referRewardAmount = (float) setting('referRewardAmount', "0");
            if ($enableReferSystem && !empty($this->referalCode)) {
                //
                $referringUser = User::where('code', $this->referalCode)->first();
                if (!empty($referringUser)) {
                    $referringUser->topupWallet($referRewardAmount);
                } else {
                    throw new Exception("Invalid referal code", 1);
                }
            }

            DB::commit();
            $this->showSuccessAlert("Account Created Successfully. Your account will be reviewed and you will be notified via email/sms when account gets approved. Thank you", 100000);
            $this->reset();
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? "An error occured please try again later",100000);
        }

    }

    public function vendorSignUp()
    {

        
        $this->validate(
            [
                "vendor_name" => "required",
                "vendor_email" => "required|email|unique:vendors,email",
                "vendor_phone" => "required|phone:" . setting('countryCode', "GH") . "|unique:vendors,phone",
                "name" => "required",
                "email" => "required|email|unique:users",
                "phone" => "required|phone:" . setting('countryCode', "GH") . "|unique:users",
                "password" => "required|string|min:6",
            ]
        );

        //
        try {

            //
            $phone = PhoneNumber::make($this->phone);
            $vendorPhone = PhoneNumber::make($this->vendor_phone);
            //
            $user = User::where('phone', $phone)->first();
            if (!empty($user)) {
                throw new Exception("Account with phone already exists", 1);
            }


            DB::beginTransaction();
            //
            $user = new User();
            $user->name = $this->name;
            $user->email = $this->email;
            $user->phone = $phone;
            $user->creator_id = Auth::id();
            $user->commission = 0.00;
            $user->password = Hash::make($this->password);
            $user->is_active = false;
            $user->save();
            //assign role
            $user->assignRole('manager');

            //create vendor
            $vendor = new Vendor();
            $vendor->name = $this->vendor_name;
            $vendor->email = $this->vendor_email;
            $vendor->phone = $vendorPhone;
            $vendor->is_active = false;
            $vendor->save();

            //assign manager to vendor 
            $user->vendor_id = $vendor->id;
            $user->save();

            DB::commit();
            $this->showSuccessAlert("Account Created Successfully. Your account will be reviewed and you will be notified via email/sms when account gets approved. Thank you", 100000);
            $this->reset();
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? "An error occured please try again later",100000);
        }

    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.auth');
    }
}
