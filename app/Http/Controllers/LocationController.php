<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Location;
use DataTables;
use DB;
use File;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.location.index');
    }

    public function getDatatables(Request $request){
        $locations = Location::orderBy('created_at', 'DESC');

        return DataTables::of($locations)
            ->addColumn('action', function ($location) {
                return '
                    <button type="button" class="btn btn-sm btn-primary edit-location" data-id="'. $location->id .'" data-name="'. $location->name .'" data-image="'. asset('locations/' . $location->image) .'" title="Edit Lokasi '. $location->name .'"><span class="fa fa-pencil"></span></button>
                    <button type="button" class="btn btn-sm btn-danger delete-location ml-1" data-location-id="'. $location->id .'" title="Hapus Lokasi '. $location->name .'"><span class="fa fa-trash"></span></button>
                    <button type="button" class="btn btn-sm btn-info detail-location ml-1" data-location-id="'. $location->id .'" title="Detail Lokasi ' . $location->name . '"><span class="fa fa-eye"></span></button>

                    <form id="deleteForm{{ $location->id }}" action="'. route('location.destroy', $location->id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.location.create'); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make([
            'name' => $request->name,
            'image' => $request->image
        ], [
            'name' => 'required|string',
            'image' => 'required|image|mimes:png,jpeg,jpg,webp'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 400);
        }

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/locations/');
                $file->move($destinationPath, $filename);

                // Create the location
                $location = Location::create([
                    'name' => $request->name,
                    'image' => $filename,
                ]);

                return response()->json(['success' => true, 'message' => 'Data baru berhasil tersimpan'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Data baru gagal tersimpan'], 422);
            }

            // return response()->json(['success' => 'Data baru berhasil tersimpan'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $location = Location::findOrFail($id);

        return response()->json([
            'name' => $location->name,
            'image' => asset('/locations/' . $location->image),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     $location = Location::find($id);
    //     return view('location.edit', compact('location'));
    // }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        return response()->json($location);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edit-name' => 'required|string',
            'edit-image' => 'nullable|image|mimes:png,jpeg,jpg,webp'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasi gagal, Harap periksa kembali',
                'errors' => $validator->errors(),
                'input' => $request->all()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $location = Location::findOrFail($request->id);

            $data = ['name' => $request->input('edit-name')];

            if ($request->hasFile('edit-image')) {
                // Hapus gambar lama
                $oldPath = public_path('locations/' . $location->image);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }

                // Simpan gambar baru
                $file = $request->file('edit-image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('locations'), $filename);
                $data['image'] = $filename;
            }

            $location->update($data);

            DB::commit();

            return response()->json([
                'success' => 'Data berhasil diperbarui',
                'location' => $location
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        try {
            $location->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data gagal terhapus'], 500);
        }
    }
}