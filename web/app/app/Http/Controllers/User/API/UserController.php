<?php
namespace App\Http\Controllers\User\API;

use App\Models\DailyUsage;
use App\Models\PaymentMethod;
use App\Models\TMM_User;
use App\Models\UsageType;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DateTime;
class UserController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function getMyProfile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update user's payment methods
     * @param [array] methods
     * @return [json] updated_methods
     */
    public function updateUserPaymentMethods(Request $request)
    {
        $request->validate([
            'methods' => 'required|array|min:1',
            'methods.*.name' => 'string|min:3|distinct',
        ]);
        $inputMethods = $request->methods;
        $user = $request->user();
        $currentMethods = $user->payment_methods()->get();

        /* Process flow
            1. Remove all current user's payment methods from input array
                1.1. After this, your input array will contain only new methods which should be added for current user
            2. Check, if there is any current method which does not exist in input array, remove it from database ( detach )
                2.1. After this, current user's payment methods will contain only methods which should be kept.
            3. Now, check methods in input array existed in database ( valid methods )
                3.1. Attach valid methods
            4. If input array is empty after 1st step, check if user is removing 1 or some ( not all ) current methods,
                or updating with same method ( not add, remove any method )
                4.1. After remove existed method from input array, push its slug to "archivedMethods" array
                4.2. Compare archivedMethods and currentMethods ( I mean the updated currentMethod (in database) after detaching )
                    if a currentMethod is not in archivedMethods, remove it
        */

        // Remove unused methods
        $archivedMethods = array();
        foreach ($currentMethods as $currentMethod){
            // this flag will mark a method which should be remove from database,
            // because that method does not exist in input array
            $unused = true;
            if(!empty($inputMethods)) {
                foreach ($inputMethods as $key => $inputMethod) {
                    if ($currentMethod->slug === $inputMethod['name']) {
                        // Remove existed method from input array
                        unset($inputMethods[$key]);
                        $unused = false;
                        // archivedMethods will be empty if all input method are not saved in database ( new methods )
                        array_push($archivedMethods,$currentMethod->slug);
                        break;
                    }
                }
                // If 1 method of current methods is not in input array, then remove it from database
                if ($unused) {
                    // find method object by its slug
                    $methodObj = PaymentMethod::where('slug', $currentMethod->slug)->first();
                    $user->payment_methods()->detach($methodObj);
                }
            }else{
                // stop the loop if input array is empty
                break;
            }
        }
        // Add new methods
        if(!empty($inputMethods)) {
            foreach ($inputMethods as $inputMethod) {
                // check new input method is existed in payment_methods table before adding to user
                $payment_method = PaymentMethod::where('slug', $inputMethod['name'])->first();
                if ($payment_method) {
                    $user->payment_methods()->attach($payment_method);
                }
            }
        }else{
            // If all input methods are removed
            if(!empty($archivedMethods)){
                // archivedMethods contains old methods that user want to keep
                $currentMethods = $user->payment_methods()->get();
                foreach ($currentMethods  as $currentMethod){
                    $unused = true;
                    foreach ($archivedMethods as $archivedMethod){
                        // if a saved method is not in archivedMethods, remove it
                        if($currentMethod->slug === $archivedMethod){
                            $unused = false;
                        }
                    }
                    if($unused){
                        $methodObj = PaymentMethod::where('slug', $currentMethod->slug)->first();
                        $user->payment_methods()->detach($methodObj);
                    }
                }
            }
        }
        return \response()->json($user->payment_methods()->get());
    }

    /**
     * Update user's balance
     * @param [string] payment_method
     * @param [double] amount
     * @return [double] updated_amount
     */
    public function updateUserBalance(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|min:3',
            'amount' => 'required|regex:/^\d+(\.\d{1,3})?$/',
        ]);
        $user = $request->user();
        $payment_method = $request->payment_method;
        $method = $user->payment_methods()->where('slug',$payment_method)->first();

        if ($method) {
            $user->payment_methods()->wherePivot('paymentmethod_id', $method->id)->updateExistingPivot($method->id, ['amount' => $request->amount]);
        }else{
            return \response()->json(
                ['message' => 'Method not found'],404
            );
        }
        return \response()->json($user->payment_methods()->get());
    }

    /**
     * Create user's daily usage
     * @param [string] payment_method
     * @param [string] usage_type
     * @param [double] paid
     * @param [double] extra
     * @param [string] description
     * @param [datetime] date
     * @return [double] updated_amount
     */
    public function addDailyUsage(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|min:3',
            'usage_type' => 'required|string|min:3',
            'paid' => 'required|regex:/^\d+(\.\d{1,3})?$/',
            'extra' => 'required|regex:/^\d+(\.\d{1,3})?$/',
            'description' => 'string|max:255',
            'date' => 'date_format:Y-m-d H:i:s',
        ]);

        $user = $request->user();
        if(isset($request->date))
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $request->date);
        $paid = $request->paid;
        $extra = $request->extra;
        $description = '';

        $payment_method = $request->payment_method;
        $method = $user->payment_methods()->where('slug',$payment_method)->first();
        if (!$method){
            return \response()->json(
                ['message' => 'Method not found'],404
            );
        }

        $usage_type = UsageType::where('slug',$request->usage_type)->first();
        if (!$usage_type){
            return \response()->json(
                ['message' => 'Usage type not found'],404
            );
        }

        if(isset($request->description) && $request->description !== '')
            $description = $request->description;


        $daily_usage = new DailyUsage([
            'user_id' => $request->user()->id,
            'paymentmethod_id' => $method->id,
            'usagetype_id' => $usage_type->id,
            'paid' => $paid,
            'extra' => $extra,
            'description' => $description,
        ]);
        if(isset($date)){
            $daily_usage->created_at = $date;
            $daily_usage->updated_at = $date;
        }

        $daily_usage->save();
        $user->payment_methods()
            ->wherePivot('paymentmethod_id', $method->id)
            ->updateExistingPivot($method->id, ['amount' => $method->amount + $request->extra - $request->paid]);
        return \response()->json([
            'daily_usage' => $daily_usage
        ]);
    }

    /**
     * Create user's daily usage
     * @param [int] record_id
     * @param [string] payment_method
     * @param [string] usage_type
     * @param [double] paid
     * @param [double] extra
     * @param [string] description
     * @param [datetime] date
     * @return [string] message
     */
    public function updateDailyUsage(Request $request)
    {
        $request->validate([
            'record_id' => 'required|integer|min:1',
            'payment_method' => 'string|min:3',
            'usage_type' => 'string|min:3',
            'paid' => 'regex:/^\d+(\.\d{1,3})?$/',
            'extra' => 'regex:/^\d+(\.\d{1,3})?$/',
            'description' => 'string|max:255',
            'date' => 'date_format:Y-m-d H:i:s',
        ]);
        $user = $request->user();
        $daily_usage = DailyUsage::find($request->record_id);
        if(!$daily_usage){
            return \response()->json(
                ['message' => 'Record not found'],404
            );
        }else{
            if(isset($request->usage_type) && $request->usage_type !== ''){
                $usage_type = UsageType::where('slug',$request->usage_type)->first();
                if (!$usage_type){
                    return \response()->json(
                        ['message' => 'Usage type not found'],404
                    );
                }
                $daily_usage->usagetype_id = $usage_type->id;
            }
            if(isset($request->date))
                $daily_usage->created_at = DateTime::createFromFormat('Y-m-d H:i:s', $request->date);
            if(isset($request->description) && $request->description !== '')
                $daily_usage->description = $request->description;

            $new_paid = 0.0;
            $new_extra = 0.0;
            $old_paid = $daily_usage->paid;
            $old_extra = $daily_usage->extra;

            if(isset($request->paid) && $request->paid != 0)
                $new_paid = $request->paid;
            if(isset($request->extra) && $request->extra != 0)
                $new_extra = $request->extra;

            if(isset($request->payment_method) && $request->payment_method !== '') {
                $new_method = $user->payment_methods()->where('slug', $request->payment_method)->first();
                if (!$new_method) {
                    return \response()->json(
                        ['message' => 'User is not own your input method'], 404
                    );
                }
                $old_method = $user->payment_methods()->where('id', $daily_usage->paymentmethod_id)->first();
                if (!$old_method) {
                    return \response()->json(
                        ['message' => 'Method not found'], 404
                    );
                }

                $daily_usage->paid = $new_paid;
                $daily_usage->extra = $new_extra;
                $daily_usage->paymentmethod_id = $new_method->id;

                $new_method_amount = $new_method->amount - $new_paid + $new_extra;
                $old_method_amount = $old_method->amount - $old_extra + $old_paid;

                $user->payment_methods()
                    ->wherePivot('paymentmethod_id', $old_method->id)
                    ->updateExistingPivot($old_method->id, ['amount' => $old_method_amount]);

                $user->payment_methods()
                    ->wherePivot('paymentmethod_id', $new_method->id)
                    ->updateExistingPivot($new_method->id, ['amount' => $new_method_amount]);

            }else{
                $method = $user->payment_methods()->where('id', $daily_usage->paymentmethod_id)->first();
                if (!$method) {
                    return \response()->json(
                        ['message' => 'Method not found'], 404
                    );
                }

                $daily_usage->paid = $new_paid;
                $daily_usage->extra = $new_extra;
                $new_amount = $method->amount - $old_extra + $old_paid - $new_paid + $new_extra;
                $user->payment_methods()
                    ->wherePivot('paymentmethod_id', $method->id)
                    ->updateExistingPivot($method->id, ['amount' => $new_amount]);
            }

            $daily_usage->update();
            return \response()->json([
                'message' => 'Update daily usage successfully.'
            ]);
        }
    }

    /**
     * Remove user's daily usage
     * @param [int] record_id
     * @return [string] message
     */
    public function removeDailyUsage(Request $request)
    {
        $request->validate([
            'record_id' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $daily_usage = DailyUsage::find($request->record_id);
        if(!$daily_usage){
            return \response()->json(
                ['message' => 'Record not found'],404
            );
        }else{
            $method = $user->payment_methods()->where('id',$daily_usage->paymentmethod_id)->first();
            if(!$method){
                return \response()->json(
                    ['message' => 'Method not found'],404
                );
            }
            $user->payment_methods()
                ->wherePivot('paymentmethod_id', $method->id)
                ->updateExistingPivot($method->id, ['amount' => $method->amount - $daily_usage->extra + $daily_usage->paid]);
            $daily_usage->delete();
            return \response()->json([
                'message' => 'Remove daily usage successfully.'
            ]);
        }
    }
}
