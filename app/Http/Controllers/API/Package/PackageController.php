<?php

namespace App\Http\Controllers\API\Package;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Package;
use Validator;
use Carbon\Carbon;
use Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Storage;

class PackageController extends Controller
{
    public function index()
    {
        $size = 100;
        $packages = DB::table('packages')->paginate($size);
        return response()->json(Package::latest('created_at')->orderBy('created_at', 'desc')->take(100)->get(), 200);
    }

    public function show(Package $slug)
    {
         // $slug->users()->with('users')->get();
        return response()->json($slug, 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $inputs = $request->only('title', 'description', 'release_on', 'rating', 'price', 'country', 'genre', 'photo');
        $inputs['user_id'] = $user->id;

        // $messages = [
        //     'mimes' => 'The :attribute must be a file of type: :values.',
        //     'max' => 'The :attribute must be maximum of: :values kb.',
        // ];

        $validator = Validator::make($inputs, [
            'photo' => 'required', // |image|mimetypes:image/jpeg,image/jpg,image/png
            'title' => 'required',
            'description' => 'required',
            'release_on' => 'required',
            'rating' => 'required',
            'price' => 'required',
            'country' => 'required',
            'genre' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()]);
        }

        $imageData = $request->get('photo');
        $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];

        $inputs['photo'] = $fileName;
        if(Image::make($request->get('photo'))->save(public_path('storage/images/').$fileName)) {
            $inputs['user_id'] = Auth::user()->id;
            $tenant = Tenant::create($inputs);
            return response()->json(['success' => true, 'tenantinfo' => $tenant], 201);
        }
        // $tenant = Tenant::create($inputs);
        // return response()->json($inputs, 200);
        return response()->json(['success' => false], 200);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $tenant->update($request->all());

        return response()->json($tenant, 200);
    }

    public function delete(Tenant $tenant)
    {
        $tenant->delete();

        return response()->json(null, 204);
    }

    public function tenantsMine()
    {
        $tenants = Tenant::where('user_id', Auth::user()->id)
               ->orderBy('created_at', 'desc')
               ->take(100)
               ->get();
        return response()->json($tenants, 200);
    }
}
