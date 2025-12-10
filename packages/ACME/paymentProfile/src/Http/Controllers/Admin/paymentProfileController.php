<?php

namespace ACME\paymentProfile\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\DataGrids\paymentProfileDataGrid;
use Webkul\Admin\DataGrids\CustomerInqueryDataGrid;
use Illuminate\Support\Facades\Storage;
use ACME\paymentProfile\Models\CustomerInquery;


class paymentProfileController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');

        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(paymentProfileDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->_config['view']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return view($this->_config['view']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }



    // sandeep ||add funtion to return inquiry index page 
    public function displayInquerys()
    {
        if (request()->ajax()) {
            return app(CustomerInqueryDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    
    // function to view inquiry 
     public function viewInquery($id)
    {
        $inquerys = CustomerInquery::find($id);
        $directory = 'customerinquery/';

    //    dd($directory);
        try {
            // Get all files from the directory
            $files = Storage::files($directory);
            // dd($files);
            $selectFiles = [];
            $prefix = $inquerys->id . '_';
    
            foreach ($files as $file) {
                $fileName = basename($file);
                if (strpos($fileName, $prefix) === 0) {
                    $selectFiles[] = $file;
                }
            }

            // dd($selectFiles);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error fetching files: ' . $e->getMessage());
        }
    
        return view('paymentprofile::admin.sales.customersInquery.view', compact('inquerys', 'selectFiles'));
       
    }

// download inquery file
    public function downloadfile($file)
    {
        $filePath = storage_path('app/public/customerinquery/' . $file);
    
        // Check if file exists
        if (!Storage::disk('public')->exists('customerinquery/' . $file)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Generate download response
        return response()->download($filePath);
    }



// function to delete inquiery
    public function destroyInquery($id)
{
    $inquery = CustomerInquery::findOrFail($id);
    try {
        if ($inquery) {
        
            $inquery->delete($id);

            return response()->json(['message' => 'Enquiry delete succesfully']);
        }
    } catch (\Exception $e) {}

    return response()->json(['message' => 'delete Failed'], 400);
}     


}
