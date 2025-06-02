<?php

namespace ACME\CateringPackage\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\DataGrids\FboDetailsDataGrid;

class AirportFboDetails extends Controller
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
    public function index($id)
    {
        if (request()->ajax()) {
            $dataGrid = app()->makeWith(FboDetailsDataGrid::class, ['id' => $id]);
            return $dataGrid->toJson();
        }

        return view($this->_config['view'], compact('id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $id = request()->query('id');

        $locale = core()->getRequestedLocaleCode();

        $countries = DB::table('countries')->get();

        $states = DB::table('country_states')->get()->toJson();

        return view($this->_config['view'])->with('countries', $countries)->with('states', $states)->with('id', $id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */

    //  sandeep remove extra fields
    public function store()
    {
        $validatedData = $this->validate(request(), [
            'name' => 'required',
            'address' => 'required',
        ]);
        // dd($validatedData);
    
        $inserted = DB::table('airport_fbo_details')->insert([
            'name' => $validatedData['name'],
            'address' => $validatedData['address'],
            'airport_id' => request()->input('airport_id'),
            'notes' => request()->input('notes')
        ]);
    
        if ($inserted) {
            session()->flash('success', trans('Airport Fbo Details created successfully'));
        } else {
            session()->flash('error', trans('Failed to create Airport Fbo Details'));
        }
    
        return redirect()->route($this->_config['redirect'], ['id' => request()->input('airport_id')]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $airportfbo = DB::table('airport_fbo_details')->find($id);

        // dd($airportfbo);

        $countries = Db::table('countries')->get();
        
        $states = Db::table('country_states')->get()->toJson();
       
        return view($this->_config['view'])->with('airportfbo', $airportfbo)->with('states', $states)->with('countries', $countries);
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */

    //  sandeep remove extra fields
    public function update(int $id)
    {
        $validatedData = $this->validate(request(), [
            'name' => 'required',
            'address' => 'required',

        ]);
        // dd($validatedData);
    
        $updated = DB::table('airport_fbo_details')
        ->where('id', $id) 
        ->update([
            'name' => $validatedData['name'],
            'address' => $validatedData['address'],
            'notes' => request()->input('notes')
        ]);
        // dd($updated);
        if ($updated) {
            
            session()->flash('success', 'Airport Fbo Details updated successfully');
        } else {
            // dd('fbdfb');
            session()->flash('error', 'Airport Fbo Details not updated');
        }
        
        return redirect()->route($this->_config['redirect'],['id' => request()->query('airport_id')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $airport_fbo = DB::table('airport_fbo_details')->find($id);
        
        try {
            
            Event::dispatch('core.settings.slider.delete.before', $id);

            DB::table('airport_fbo_details')->where('id', $id)->delete();
            
            Event::dispatch('core.settings.slider.delete.after', $id);

            return response()->json(['message' => trans('Airport fbo deleted successfully', ['name' => 'Airport'])]);
        } catch (\Exception $e) {

            report($e);
        }       

        return response()->json(['message' => trans('admin::app.response.delete-failed', ['name' => 'Airport'])], 500);
    }
}
