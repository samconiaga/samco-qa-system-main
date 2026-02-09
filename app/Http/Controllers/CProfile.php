<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Traits\ResponseOutput;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\ChangePhotoRequest;
use App\Repositories\App\AppRepository;

class CProfile extends Controller
{
    use ResponseOutput;
    protected $appRepository;
    public function __construct(AppRepository $appRepository)
    {
        $this->appRepository = $appRepository;
    }
    public function index()
    {
        return inertia('Profile/Index', [
            'deparment' => Department::select('id', 'name')->get(),
        ]);
    }

    public function update(ProfileRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {

            $data = $request->validated();
            $user = User::findOrFail(Auth::id());
            $employee = Employee::where('user_id', $user->id)->firstOrFail();
            if ($request->hasFile('sign')) {
                $data['sign'] = $request->file('sign')->store('sign', 'public');
            }
            if (empty($data['password'])) {
                unset($data['password']);
            }
            $user->update([
                'name'     => $data['name'] ?? $user->name,
                'email'    => $data['email'] ?? $user->email,
                'password' => $data['password'] ?? $user->password,
            ]);
            $employee->update([
                'name'    => $data['name'] ?? $employee->name,
                'phone'   => $data['phone'] ?? $employee->phone,
                'address' => $data['address'] ?? $employee->address,
                'sign'    => $data['sign'] ?? $employee->sign,
            ]);

            return redirect()->back()->with('success', __('Profile updated successfully'));
        });
    }

    public function changePhoto(ChangePhotoRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $user = User::find(Auth::id());
            $this->appRepository->updateOneModelWithFile($user, [], 'photo', 'images/user');
            return back()->with('success', __("Photo Changed Successfully"));
        });
    }
}
