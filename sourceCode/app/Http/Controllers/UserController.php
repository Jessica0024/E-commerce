<?php

namespace App\Http\Controllers;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Function to show the update form for a user
    function showUpdate(User $user)
    {
        return view('editUser', compact('user'));
    }

    // Function to update the user's information
    function updateUser(Request $req)
    {
        // Get the user's current data
        $data = User::find($req->id);
        
        // Validate the request data
        $req->validate([
            'name' => ['required', 'string', 'max:255'],
            'profile_picture' => ['nullable|image|max:2048'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($data->id)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phoneNo' => ['required', 'regex:/^(01\d-\d{7}|011-\d{8})$/'],
            'address' => ['max:255'],
        ]);
       
        // If a new profile picture is uploaded, store it and update the user's profile picture
        if ($req->hasFile('profile_pic')) {
            $profilePic = $req->file('profile_pic');
            $filename = time() . '_' . $profilePic->getClientOriginalName();
            $profilePic->move(public_path('images/profilePicture'), $filename);

            // Delete the user's old profile picture
            if ($data->profile_pic !== 'default.jpg') {
                $oldProfilePic = public_path('images/profilePicture/' . $data->profile_pic);
                if (file_exists($oldProfilePic)) {
                    unlink($oldProfilePic);
                }
            }

            // Save the filename to the user model
            $data->profile_pic = $filename;
        }

        // Update the user's information
        $data->name = $req->name;
        $data->email = $req->email;
        $data->password = Hash::make($req->password);
        $data->phoneNo = $req->phoneNo;
        
        // If the user is authorized to update their address, update it
        if (Gate::allows('isUser')) {
            $data->address = $req->address;
        }

        $data->save();
        
        // Redirect the user to their account page
        return redirect()->route('myAccount');
    }

    public function facebookRedirect()
    {
        return Socialite::driver('facebook')->redirect();
    }
    
    public function facebook()
    {
        $user = Socialite::driver('facebook')->user();
    }}
