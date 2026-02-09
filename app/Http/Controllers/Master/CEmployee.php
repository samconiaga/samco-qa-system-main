<?php

namespace App\Http\Controllers\Master;

use App\Models\User;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\App\AppRepository;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Master\EmployeeRequest;
use App\Notifications\EmailNotification;
use App\Repositories\SendNotification\SendNotificationRepository;
use App\Traits\ResponseOutput;
use Illuminate\Support\Facades\DB;
use LaravelEasyRepository\Traits\Response;

class CEmployee extends Controller
{
    use ResponseOutput;
    protected $appRepository, $sendNotificationRepository;
    public function __construct(AppRepository $appRepository, SendNotificationRepository $sendNotificationRepository)
    {
        $this->appRepository = $appRepository;
        $this->sendNotificationRepository = $sendNotificationRepository;
    }
    public function index()
    {
        return inertia('Master/Employees');
    }

    public function create()
    {
        return inertia('Master/Partials/Employees/Form', [
            'title' => __('Add New Employee'),
            'positions' => Position::cursor(),
            'departments' => Department::cursor(),
            'permissions' => Permission::cursor()
        ]);
    }

    public function store(EmployeeRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            DB::transaction(function () use ($request) {
                $data = $request->validated();
                $password = "12345678";
                $userData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $password
                ];

                $user = $this->appRepository->insertOneModel(new User(), $userData);
                $user->assignRole('Employee');
                $user->syncPermissions($data['permissions'] ?? []);
                $employeeData = [
                    'user_id' => $user->id,
                    'position_id' => $data['position_id'],
                    'department_id' => $data['department_id'],
                    'employee_code' => $data['employee_code'],
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'address' => $data['address'],
                    'phone' => $data['phone'],
                ];
                $this->appRepository->insertOneModel(new Employee(), $employeeData);

                $this->sendNotification($user, [
                    'name' => $data['name'],
                    'password' => $password,
                    'email' => $data['email'],
                    'employee_code' => $data['employee_code'],
                ]);
            });
            return redirect()->route('master-data.employees.index')->with('success', __('Employee added successfully'));
        });
    }

    public function edit(Employee $employee)
    {
        return inertia('Master/Partials/Employees/Form', [
            'title' => __('Edit Employee'),
            'employee' => $employee->load('user'),
            'positions' => Position::cursor(),
            'departments' => Department::cursor(),
            'permissions' => Permission::cursor()
        ]);
    }
    public function update(EmployeeRequest $request, Employee $employee)
    {
        return $this->safeInertiaExecute(function () use ($request, $employee) {
            DB::transaction(function () use ($request, $employee) {
                $data = $request->validated();
                $user = $employee->user;
                if ($user) {
                    $userData = [
                        'name' => $data['name'],
                        'email' => $data['email'],
                    ];
                    $user->update($userData);
                    $user->syncPermissions($data['permissions'] ?? []);
                }

                $employeeData = [
                    'position_id' => $data['position_id'],
                    'department_id' => $data['department_id'],
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'address' => $data['address'],
                    'phone' => $data['phone'],
                ];
                $this->appRepository->updateOneModel($employee, $employeeData);
            });
            return redirect()->route('master-data.employees.index')->with('success', __('Employee updated successfully'));
        });
    }

    public function destroy(Employee $employee)
    {
        return $this->safeInertiaExecute(function () use ($employee) {
            DB::transaction(function () use ($employee) {
                $this->appRepository->deleteOneModel($employee);
                $employee?->user?->delete();
            });
            return redirect()->route('master-data.employees.index')->with('success', __('Employee deleted successfully'));
        });
    }

    protected function sendNotification($user, $data)
    {
        $this->sendNotificationRepository->sendEmailMessageWithAttachment(
            $user,
            $data,
            'new-user-notification.txt',
            __('Registration Successful')
        );

        $this->sendNotificationRepository->sendWhatsappMessage(
            $user->employee->phone,
            $data,
            'new-user-notification.txt'
        );
    }
}
