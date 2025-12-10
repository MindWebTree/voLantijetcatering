<?php

namespace ACME\CateringPackage\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Webkul\Admin\DataGrids\CateringPackageDataGrid;
use ACME\CateringPackage\Repositories\CateringPackageRepository;
use Illuminate\Support\Facades\Event;
use DB;
use Illuminate\Http\Request;

//use Webkul\Core\Repositories\SliderRepository;

class CateringPackageController extends Controller
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
    public function __construct(protected CateringPackageRepository $CateringPackageRepository)
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
            return app(CateringPackageDataGrid::class)->toJson();
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

        $locale = core()->getRequestedLocaleCode();

        // $countries = Db::table('countries')->get();
        $countries = Db::table('countries')->where('name', 'United States')->get();

        $states = Db::table('country_states')->where('country_code', 'US')->get();

        // echo $states1 =  json_encode($states);

        //echo $states1->country_code;

        //var_dump($states);
        return view($this->_config['view'])->with('countries', $countries)->with('states', $states);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

     
        $this->validate(request(), [
            'name' => 'string|required',
            'address' => 'required',
            'zipcode' => 'required',
            'latitude' => 'required|integer|numeric|between:0,99.99',
            'longitude' => 'required|integer|numeric|between:0,99.99',
            'display_order' => 'required',
            // 'active' => 'required',
        ]);

        $requestData = request()->all();
        if (isset($requestData['active']) && $requestData['active'] == "on") {
            $requestData['active'] = 1;
        } else {
            $requestData['active'] = 0;
        }

        $this->CateringPackageRepository->create($requestData);
   
        session()->flash('success', trans('admin::app.settings.cateringpackages.create-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {

        $airport = $this->CateringPackageRepository->findOrFail($id);

        $countries = Db::table('countries')->where('name', 'United States')->get();

        $states = Db::table('country_states')->where('country_code', 'US')->get()->toJson();


        return view($this->_config['view'])->with('airport', $airport)->with('states', $states)->with('countries', $countries);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {

        $this->validate(request(), [
            'name' => 'string|required',
            'address' => 'required',
            'zipcode' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'display_order' => 'required',
            // 'active' => 'required',
        ]);
        $requestData = request()->all();
        if (isset($requestData['active']) && $requestData['active'] == "on") {
            $requestData['active'] = 1;
        } else {
            $requestData['active'] = 0;
        }

        $this->CateringPackageRepository->update($requestData, $id);

        session()->flash('success', trans('admin::app.settings.cateringpackages.update-success'));

        return redirect()->route($this->_config['redirect']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $this->CateringPackageRepository->findOrFail($id);

        try {

            Event::dispatch('core.settings.slider.delete.before', $id);

            // $this->CateringPackageRepository->delete($id);
            $this->CateringPackageRepository->findOrFail($id)->forceDelete();

            Event::dispatch('core.settings.slider.delete.after', $id);

            return response()->json(['message' => 'Airport removed Sucessfully.'], 200);
        } catch (\Exception $e) {

            report($e);
        }

        return response()->json(['message' => trans('admin::app.response.delete-failed', ['name' => 'Airport'])], 500);
    }


    public function getStates(Request $request)
    {
        echo $request->country_id;
        echo "this is only testing purpose";

        //   $data['states'] = DB::table('country_states')->select('default_name')->where('country_id','=',$request->country_id)->get();
        // return response()->json($data);

    }
}
