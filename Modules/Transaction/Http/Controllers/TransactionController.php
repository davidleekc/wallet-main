<?php

namespace Modules\Transaction\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Log;
use DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_clients', ['only' => ['index', 'index_list', 'index_data', 'show']]);

        // Page Title
        $this->module_title = 'Transactions';

        // module name
        $this->module_name = 'transactions';

        // directory path of the module
        $this->module_path = 'transactions';

        // module icon
        $this->module_icon = 'fas fa-sitemap';

        // module model name, path
        $this->module_model = "Bavix\Wallet\Models\Transaction";
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'List';

        $$module_name = $module_model::paginate();

        if($request->ajax()){
            $$module_name = $module_model::leftJoin('clients', 'clients.id', '=', 'transactions.payable_id')
            ->select('transactions.*', 'clients.phone')
            ->where(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                       $search = $request->get('search');
                       $w->orWhere('transactions.id', 'LIKE', "%$search%")
                       ->orWhere('transactions.uuid', 'LIKE', "%$search%")
                       ->orWhere('clients.phone', 'LIKE', "%$search%")
                       ->orWhere('transactions.type', 'LIKE', "%$search%")
                       ->orWhere('transactions.amount', 'LIKE', "%$search%")
                       ->orWhere('transactions.meta', 'LIKE', "%$search%");
                   });
                }
            })->get();

            return datatables()->of($$module_name)->toJson();
            //return response()->json($$module_name);
        }

        Log::info(label_case($module_title.' '.$module_action).' | User:'.auth()->user()->name.'(ID:'.auth()->user()->id.')');

        return view('transaction::index',
            //compact('module_title', 'module_name', 'module_icon', 'module_name_singular', 'module_action')
            compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_name_singular', 'module_action')
        );
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('transaction::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('transaction::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('transaction::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
