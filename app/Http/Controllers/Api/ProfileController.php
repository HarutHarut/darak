<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Profile\Edit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, DB, Hash, Storage};
use \Throwable;

class ProfileController extends ApiController
{
    public function edit(Edit $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            foreach ($data as $key => $value) {

                if ($key == 'password') {
                    $user->password = Hash::make($data['password']);
                }

                if ($key == 'avatar') {
                    if ($user->avatar) {
                        Storage::delete(str_replace('/storage/', '', $user->avatar));
                    }
                    $user->avatar = Storage::url(Storage::putFile('profile', $value));
                }

                if ($key == 'name' || $key == 'language') {
                    $user->{$key} = $value;
                }
            }

            $user->save();
            DB::commit();
            return $this->success(200, ['user' => $user], 'Profile updated successfully.');
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'ProfileController edit action', $user->id);
            return $this->error(400, "Profile updating failed");
        }
    }
    public function update(Request $request) {
        $user = Auth::user();
        $data = $request->all();
        $user->update([
            "name" => $data['name'],
            "last_name" => $data['last_name']
        ]);
        return $this->success(200, ["massage" => "Name Changed Successfully"]);
    }
    public function changePassword(Request $request) {
        $user = Auth::user();
        $password = Hash::make($request->input("password"));
        $user->update([
            "password" => $password
        ]);
        return $this->success(200, ["massage" => "Password Changed Successfully"]);
    }
}
