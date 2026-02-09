<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CapaType;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\ChangeRequest;
use App\Models\ScopeOfChange;
use App\Models\HalalAssesment;
use App\Models\StimuliOfChange;
use App\Models\RegulatoryAssesment;
use Yajra\DataTables\Facades\DataTables;
use App\Models\FacilityChangeAuthorization;
use App\Models\Issue;

class CDatatable extends Controller
{
    // MASTER DATA
    public function departments(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = Department::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->whereRaw('name ILIKE ?', ["%{$search}%"]);
            })
            ->paginate($perPage);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }
    public function positions(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = Position::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->whereRaw('name ILIKE ?', ["%{$search}%"]);
            })
            ->paginate($perPage);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }
    public function products(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = Product::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->whereRaw('name ILIKE ?', ["%{$search}%"]);
            })
            ->paginate($perPage);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }
    public function productTrash(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = Product::onlyTrashed()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->whereRaw('name ILIKE ?', ["%{$search}%"]);
            })
            ->paginate($perPage);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }

    public function capaTypes(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = CapaType::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->whereRaw('name ILIKE ?', ["%{$search}%"]);
            })
            ->paginate($perPage);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }

    public function capaTypeTrash(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = CapaType::onlyTrashed()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->whereRaw('name ILIKE ?', ["%{$search}%"]);
            })
            ->paginate($perPage);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }
    public function employees(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = Employee::with(['user'])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('name ILIKE ?', ["%{$search}%"])
                        ->orWhereRaw('gender ILIKE ?', ["%{$search}%"])
                        ->orWhereHas('department', fn($q) => $q->whereRaw('name ILIKE ?', ["%{$search}%"]))
                        ->orWhereHas('position', fn($q) => $q->whereRaw('name ILIKE ?', ["%{$search}%"]))
                        ->orWhereHas('user', fn($q) => $q->whereRaw('email ILIKE ?', ["%{$search}%"]));
                });
            })
            ->paginate($perPage);


        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }
    // ATTRIBUTE TYPES


    public function scopeOfChanges(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = ScopeOfChange::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->whereRaw('name ILIKE ?', ["%{$search}%"]);
            })
            ->paginate($perPage);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }

    // CHANGE REQUEST 

    public function changeRequests()
    {
        $perPage = request()->get('per_page', 10);
        $search = request()->get('search');

        $data = ChangeRequest::query()
            ->select('id','title', 'request_number', 'initiator_name', 'employee_id', 'department_id', 'overall_status','requested_date', 'created_at')
            ->byEmployee()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('request_number', 'ILIKE', "%{$search}%")
                        ->orWhere('initiator_name', 'ILIKE', "%{$search}%")
                        ->orWhereHas('employee.user', function ($u) use ($search) {
                            $u->where('email', 'ILIKE', "%{$search}%");
                        });
                });
            })
            ->with([
                'employee.user:id,name,email',
                'department:id,name',
                'lastApproval:id,change_request_id,comments',
                'approvals:decision,approver_id,change_request_id',
            ])
            ->latest()
            ->paginate($perPage);
        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }

    public function affectedProducts(Request $request, ChangeRequest $changeRequest)
    {
        $perPage = $request->get('per_page', 10);
        $data = $changeRequest->affectedProducts()
            ->paginate($perPage);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }
    public function relatedDepartmentAssessment(Request $request, ChangeRequest $changeRequest)
    {
        $perPage = $request->get('per_page', 10);
        $data = $changeRequest->followUpImplementations()
            ->with(['employee', 'department'])
            ->paginate($perPage);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }

    public function followUpImplementations(Request $request, ChangeRequest $changeRequest)
    {
        $perPage = $request->get('per_page', 10);
        $data = $changeRequest->actionPlans()
            ->with(['department', 'impactCategory', 'pic','completionProofFiles'])
            ->paginate($perPage);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
        ]);
    }

    // CAPA
    public function issues(Request $request)
    {
        $data = Issue::select('id', 'issue_number', 'subject', 'deadline', 'department_id', 'capa_type_id', 'status', 'created_at')
            ->byEmployee()
            ->when(
                $request->search,
                fn($q, $v) =>
                $q->whereAny(['issue_number', 'subject', 'status'], 'ILIKE', "%$v%")
                    ->orWhereHas('department', fn($d) => $d->where('name', 'ILIKE', "%$v%"))
                    ->orWhereHas('capaType', fn($c) => $c->where('name', 'ILIKE', "%$v%"))
            )
            ->when($request->status && $request->status !== 'All', fn($q) => $q->where('status', $request->status))
            ->with(['department:id,name', 'capaType:id,name'])
            ->latest()
            ->paginate($request->get('per_page', 10));

        return response()->json($data);
    }
}
