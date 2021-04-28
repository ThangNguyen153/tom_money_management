<?php


namespace App\Http\Controllers\User\API;

use App\Http\Controllers\Controller;
use App\Models\DailyUsage;
use App\Models\UsageType;
use Illuminate\Http\Request;
use DateTime;
class DailyUsageController extends Controller
{
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
            'payment_method' => 'required|string|min:3|max:50',
            'usage_type' => 'required|string|min:3|max:50',
            'paid' => 'required|regex:/^\d+(\.\d{1,3})?$/',
            'extra' => 'required|regex:/^\d+(\.\d{1,3})?$/',
            'description' => 'string|min:3|max:255',
            'date' => 'date_format:Y-m-d H:i:s',
        ]);

        $user = $request->user();

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

        $paid = $request->paid;
        $extra = $request->extra;
        $description = '';

        if(isset($request->description))
            $description = $request->description;

        $daily_usage = new DailyUsage([
            'user_id' => $request->user()->id,
            'paymentmethod_id' => $method->id,
            'usagetype_id' => $usage_type->id,
            'paid' => $paid,
            'extra' => $extra,
            'description' => $description,
        ]);

        if(isset($request->date)){
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $request->date);
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
     * Update user's daily usage
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
            'usage_type' => 'string|min:3|max:50',
            'paid' => 'regex:/^\d+(\.\d{1,3})?$/',
            'extra' => 'regex:/^\d+(\.\d{1,3})?$/',
            'description' => 'string|min:3|max:255',
            'date' => 'date_format:Y-m-d H:i:s',
        ]);
        $user = $request->user();
        $daily_usage = DailyUsage::find($request->record_id);
        if(!$daily_usage){
            return \response()->json(
                ['message' => 'Record not found'],404
            );
        }else{
            if(isset($request->usage_type)){
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
            if(isset($request->description))
                $daily_usage->description = $request->description;

            $new_paid = 0.0;
            $new_extra = 0.0;
            $old_paid = $daily_usage->paid;
            $old_extra = $daily_usage->extra;

            if(isset($request->paid) && $request->paid != 0)
                $new_paid = $request->paid;
            if(isset($request->extra) && $request->extra != 0)
                $new_extra = $request->extra;

            if(isset($request->payment_method)) {
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
