<?php
namespace App\Http\Controllers\Auth\Web;

use App\Http\Controllers\Controller;
use App\Models\UsageType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\Web\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
class AccessController extends Controller
{
    public function showLoginForm(){
        if (Auth::check()) {
            return redirect()->route('user-daily-usage');
        } else {
            return view('login');
        }
    }

    public function login(Request $request){
        $validator = Validator::make(['email' => $request->email, 'password' => $request->password], [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect('/login')
                ->withErrors($validator)
                ->withInput();
        }

        if (Auth::viaRemember()){
            return redirect()->route('user-daily-usage');
        }else{
            $credentials = $request->only(['email', 'password'], $request->remember_me);
            if (Auth::attempt($credentials)) {
                return redirect()->route('user-daily-usage');
            } else {
                return redirect()->back()->withInput()->with('status', 'Email or Password is incorrect');
            }
        }
    }

    public function logout(Request $request){
        Auth::logout();
        return redirect()->route('login-form');
    }

    public function getDailyUsage(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();

            if($user->roles->first()->name !== 'user'){
                return response()->json(['message' => 'You don\'t have permission. User Only'],403);
            }
            $userPaymentMethods = $user->payment_methods()->get();
            $usagetypes = UsageType::orderBy('name', 'ASC')->get();
            $daily_usages = $user->daily_usages()
                                ->whereYear('created_at', '=', now()->year)
                                ->whereMonth('created_at', '=', now()->month)
                                ->orderBy('created_at', 'DESC')
                                ->paginate(30)
                                ->withPath('/user/daily-usage');
            return view('daily-usage', ['daily_usages' => $daily_usages,
                'userPaymentMethods' => $userPaymentMethods,
                'usagetypes' => $usagetypes,
            ]);
        }else{
            return redirect()->route('login-form');
        }

    }
    public function getUsageStatistics(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            if($user->roles->first()->name !== 'user'){
                return response()->json(['message' => 'You don\'t have permission. User Only'],403);
            }
            return view('statistics');
        }else{
            return redirect()->route('login-form');
        }
    }
    public function getActivityLog(Request $request) {
        if (Auth::check()) {
            $user = Auth::user();
            if($user->roles->first()->name !== 'user'){
                return response()->json(['message' => 'You don\'t have permission. User Only'],403);
            }
            $activities = Activity::where('causer_id',$user->id)
                                    ->orderBy('created_at', 'DESC')
                                    ->paginate(30)
                                    ->withPath('/user/activities');
            return view('activity-log', ['activities'=>$activities]);
        }else{
            return redirect()->route('login-form');
        }
    }
}
