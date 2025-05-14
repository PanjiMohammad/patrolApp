<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Security;
use App\User;
use Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }

    public function showLoginForm(){
        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Harap isi email & password terlebih dahulu', 'errors' => $validator->errors(), 'input' => $request->all()], 400);
        }
        
        // untuk match
        // $security = Security::where('email', $request->email)->first();

        // if ($security) {
        //     dd([
        //         'input_password' => $request->password,
        //         'stored_hashed' => $security->password,
        //         'match' => \Hash::check($request->password, $security->password)
        //     ]);
        // }

        // Check if email exists in seller or web (super admin) tables
        $securityExists = Security::where('email', $request->email)->exists();
        $superAdminExists = User::where('email', $request->email)->exists();

        if (!$securityExists && !$superAdminExists) {
            return response()->json(['error' => 'Email tidak terdaftar'], 400);
        }

        // Credentials for authentication
        $credentials = $request->only('email', 'password');

        if ($securityExists) {
            // $credentials['status'] = 1; // Only attempt for sellers with status 1

            if (auth()->guard('security')->attempt($credentials)) {
                $security = auth()->guard('security')->user();
                // dd($security);
                // if ($security->status == '0') {
                //     return response()->json(['error' => 'Email belum diverifikasi'], 400);
                // }
                return response()->json(['success' => 'Login berhasil', 'redirect' => route('security.dashboard')], 200);
            }
        }

        if ($superAdminExists && auth()->guard('web')->attempt($credentials)) {
            return response()->json(['success' => 'Login berhasil', 'redirect' => route('home')], 200);
        }

        // Return error if authentication fails
        return response()->json(['error' => 'Email / Password salah, Silahkan coba lagi'], 500);
    }

    public function newRegister(){
        return view('auth.register');
    }

    public function postRegister(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone_number' => 'required',
            'email' => 'required|email',
            'address' => 'required|string'
        ]);

        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors(), 'message' => 'Gagal Menyimpan', 'input' => $request->all()], 400);
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        try {
            if (Security::where('email', $request->email)->exists()) {
                return response()->json(['error' => 'Email Sudah Ada, Silahkan Coba Lagi.'], 409);
            }

            $password = Str::random(8); 
            $seller = Security::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $password, 
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'activate_token' => Str::random(30),
                'status' => false
            ]);

            // Mail::to($request->email)->send(new SellerRegisterMail($seller, $password));

            return response()->json(['success' => 'Registrasi berhasil, harap menunggu respon dari admin'], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan, silakan coba lagi.'], 500);
        }
    }

    public function resetPassword(){
        return view('auth.passwords.reset');
    }

    public function postResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors(), 'message' => 'Gagal Menyimpan', 'input' => $request->all()], 400);
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                // Hash password baru menggunakan bcrypt
                $hashedPassword = 'admin123';
                
                // Update password user
                $user->password = $hashedPassword;
                $user->save();
                
                return response()->json(['success' => 'Password berhasil diupdate menjadi admin123'], 200);
            }

            return response()->json(['error' => 'Pengguna tidak ditemukan!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        // try {
        //     $data = Seller::where('email', $request->email)->first();

        //     if($data != null){
        //         $seller = Seller::find($data->id);
        //         $password = Str::random(8); 
        //         $seller->update([
        //             'password' => $password,
        //             'activate_token' => Str::random(30),
        //             'status' => 0
        //         ]);

        //         // Mail::to($request->email)->send(new SellerResetPasswordMail($seller, $password));

        //         return response()->json(['success' => true, 'message' => 'Atur Ulang Kata Sandi Berhasil, Silahkan Cek Email.']);
        //     } else {
        //         return response()->json(['error' => true, 'message' => 'Atur Ulang Kata Sandi Gagal, Email Tidak Terdaftar.']);
        //     }

        // } catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()]);
        // }
    }

    public function securityPostResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            // return response()->json(['error' => $validator->errors(), 'message' => 'Gagal Menyimpan', 'input' => $request->all()], 400);
            return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        try {
            // Cari user berdasarkan email
            $user = Security::where('email', $request->email)->first();
            
            if ($user) {
                // Hash password baru menggunakan bcrypt
                $hashedPassword = 'admin123';
                
                // Update password user
                $user->password = $hashedPassword;
                $user->save();
                
                return response()->json(['success' => 'Password berhasil diupdate menjadi admin123'], 200);
            }

            return response()->json(['error' => 'Pengguna tidak ditemukan!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        // try {
        //     $data = Seller::where('email', $request->email)->first();

        //     if($data != null){
        //         $seller = Seller::find($data->id);
        //         $password = Str::random(8); 
        //         $seller->update([
        //             'password' => $password,
        //             'activate_token' => Str::random(30),
        //             'status' => 0
        //         ]);

        //         // Mail::to($request->email)->send(new SellerResetPasswordMail($seller, $password));

        //         return response()->json(['success' => true, 'message' => 'Atur Ulang Kata Sandi Berhasil, Silahkan Cek Email.']);
        //     } else {
        //         return response()->json(['error' => true, 'message' => 'Atur Ulang Kata Sandi Gagal, Email Tidak Terdaftar.']);
        //     }

        // } catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()]);
        // }
    }

    public function logout(Request $request){
        Auth::logout();
        return response()->json([
            'success' => 'Berhasil keluar dari halaman', 
            'redirect' => route('login')
        ], 200);
    }
}
