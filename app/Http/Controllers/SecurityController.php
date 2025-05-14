<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Security;
use DataTables;
use DB;

class SecurityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.security.index');
    }

    public function getDatatables(Request $request){
        $securitys = Security::orderBy('created_at', 'DESC');

        return DataTables::of($securitys)
            ->addColumn('action', function ($security) {
                return '
                    <a href="'. route('security.edit', $security->id) .'" class="btn btn-sm btn-primary mr-1" title="Edit Satpam '. $security->name .'"><span class="fa fa-pencil"></span></a>
                    <button type="button" class="btn btn-sm btn-danger delete-security" data-security-id="'. $security->id .'" title="Hapus Satpam '. $security->name .'"><span class="fa fa-trash"></span></button>
 
                    <form id="deleteForm{{ $security->id }}" action="'. route('security.destroy', $security->id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->addColumn('status', function ($security) {
                if ($security->status == 1) {
                    return '<span class="badge badge-success">Aktif</span>';
                }

                if ($security->status == 0) {
                    return '<span class="badge badge-danger">Tidak Aktif</span>';
                }

                if($security->status !== 0 && $security->activate_token !== null){
                    return '<span class="badge badge-secondary">Belum Aktivasi</span>';
                }
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.security.create'); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone_number' => 'required',
            'address' => 'required|string',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        if(Security::where('email', $request->email)->exists()){
            return response()->json(['error' => 'Email sudah ada'], 400);
        } else {
            try {
                // password dibuat otomatis
                $password = Str::random(8);

                $security = Security::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'password' => $password, 
                    'phone_number' => $request->phone_number,
                    'activate_token' => null,
                    'status' => $request->status
                ]);


                return response()->json(['success' => 'Data baru berhasil tersimpan'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $security = Security::find($id);
        return view('admin.security.edit', compact('security'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'status' => 'required'
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

            $security = Security::findOrFail($request->security_id);

            
            $data = $request->only(['name', 'email', 'phone_number', 'address', 'status']);
            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }

            $data['activate_token'] = null;

            $security->update($data);

            DB::commit();

            return response()->json(['success' => 'Data berhasil diperbarui', 'security' => $security], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
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
        $security = Security::find($id);

        if (!$security) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        try {
            $security->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data gagal terhapus'], 500);
        }
    }
}
