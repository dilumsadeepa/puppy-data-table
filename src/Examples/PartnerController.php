<?php

namespace Dilum\PuppyDataTable\Examples;

use App\Http\Controllers\Controller;
use Dilum\PuppyDataTable\DataTable;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        return DataTable::of(Partner::query())
            ->with(['profile'])
            ->addColumn('actions', function($row) {
                return '<a href="/partners/'. $row->id .'/edit">Edit</a>';
            })
            ->editColumn('name', function($row) {
                return strtoupper($row->name);
            })
            ->rawColumns(['actions'])
            ->searchable(['name','email','mobile_number','business_name'])
            ->orderColumn('created_at', function($query, $dir) {
                $query->orderBy('created_at', $dir);
            })
            ->toResponse($request);
    }
}
