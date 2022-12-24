<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $umkm = $this->user->profile->umkm;
 

        if (!$umkm) {
            return $this->sendResponse('No profile found', "",  404, false);
        }
        return $this->sendResponse("Profile found", $umkm, 200, true);
    }

    public function store(Request $request)
    {
        //validate
        $validator = Validator::make($request->all(), [
            'address' => 'string',
            'phone' => 'string',
        ]);

        if ($validator->fails())
            return $this->sendResponse('Sorry, validation error. Please fill all fields.', $validator->errors(), 422, false);


        $this->user = JWTAuth::parseToken()->authenticate();
        $id = $this->user->id;

        if ($this->user->profile()->exists())
            return $this->sendResponse('Profile already exists', "",  422, false);


        $profile = new Profile([
            'address' => $request['address'],
            'phone' => $request['phone'],
            'user_id' => $id
        ]);

        if (!$this->user->profile()->save($profile))
            return $this->sendResponse("Profile not created", "",  404, false);

        return $this->sendResponse("Profile created", $profile, 201, true);
    }

    public function show($id)
    {
        $profile = Profile::find($id);
        if (!$profile) {
            return $this->sendResponse('Profile with id ' . $id . ' cannot be found', "",  404, false);
        }

        return $this->sendResponse("Profile found", $profile, 200, true);
    }

    public function update(Request $request, $id)
    {

        //validator
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'phone' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse('Validation error.  profile could not be updated.', $validator->errors(), 400, false);
        }

        $profile = Profile::find($id);
        if (!$profile)
            return $this->sendResponse('Profile with id ' . $id . ' cannot be found', "",  404, false);

        $updated = $profile->fill($request->all())->save();

        if (!$updated)
            return $this->sendResponse('Profile could not be updated', "",  404, false);

        return $this->sendResponse("Profile updated", $profile, 200, true);
    }

    public function destroy($id)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $iduser = $this->user->id;

        if ($iduser != $id)
            return $this->sendResponse('You cannot delete this profile', "",  404, false);


        $profile = Profile::find($id);
        if (!$profile)
            return $this->sendResponse('Profile with id ' . $id . ' cannot be found', "",  404, false);

        if (!$profile->delete())
            return $this->sendResponse('Profile could not be deleted', "",  404, false);

        return $this->sendResponse("Profile deleted", "", 200, true);
    }
}
