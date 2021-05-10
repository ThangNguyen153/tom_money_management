<?php


namespace App\Http\Controllers\User\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\TMM_User;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface;
use Illuminate\Http\Request;
class PaymentMethodController extends Controller
{
    /**
     * @var PaymentMethodRepositoryInterface
     */
    protected $paymentMethodRepo;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepo)
    {
        $this->paymentMethodRepo = $paymentMethodRepo;
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
                    $methodObj = $this->paymentMethodRepo->findBy('slug', $currentMethod->slug);
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
                $payment_method = $this->paymentMethodRepo->findBy('slug', $inputMethod['name']);
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
                        $methodObj = $this->paymentMethodRepo->findBy('slug', $currentMethod->slug);
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
     * @param [string] description
     * @return [double] updated_amount
     */
    public function updateUserBalance(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|min:3',
            'amount' => 'required|regex:/^\d+(\.\d{1,3})?$/',
            'description' => 'string|min:3|max:255'
        ]);
        $user = $request->user();
        $payment_method = $request->payment_method;
        $method = $user->payment_methods()->where('slug',$payment_method)->first();
        $description = 'updated user balance';
        if(isset($request->description)){
            $description = $request->description;
        }
        if ($method) {
            self::handleUpdateUserBalance($user,$method,$request->amount, $request->ip(),$description);
        }else{
            return \response()->json(
                ['message' => 'Method not found'],404
            );
        }
        return \response()->json($user->payment_methods()->get());
    }

    /**
     * Transfer user's balance from current payment method to new payment method
     * @param [string] current_payment_method
     * @param [string] new_payment_method
     * @param [double] amount
     * @return [string] message
     */
    public function transferUserBalance(Request $request)
    {
        $request->validate([
            'current_payment_method' => 'required|string|min:3',
            'new_payment_method' => 'required|string|min:3|different:current_payment_method',
            'amount' => 'required|regex:/^\d+(\.\d{1,3})?$/',
        ]);
        $user = $request->user();
        $current_method = $user->payment_methods()->where('slug',$request->current_payment_method)->first();
        $new_method = $user->payment_methods()->where('slug',$request->new_payment_method)->first();

        if ($current_method && $new_method) {
            // subtract current payment method balance
            $reason = 'Transfered to ' . $new_method->name;
            self::handleUpdateUserBalance($user,$current_method,$current_method->amount - $request->amount, $request->ip(),$reason);
            // add new payment method balance
            $reason = 'Transfered from ' . $current_method->name;
            self::handleUpdateUserBalance($user,$new_method,$new_method->amount + $request->amount, $request->ip(),$reason);
        }else{
            return \response()->json(
                ['message' => 'Method not found'],404
            );
        }
        return \response()->json(['message'=>'Transfered successfully']);
    }

    /**
     * Transfer user's balance from current payment method to new payment method
     * @param [TMM_User] user
     * @param [PaymentMethod] paymentMethod
     * @param [double] amount
     * @param [string] userIP
     * @param [string] reason
     * @return [boolean]
     */
    public function handleUpdateUserBalance(TMM_User $user, PaymentMethod $paymentMethod, float $amount, string $userIP ='', string $reason=''){
        $old_amount = $paymentMethod->amount;
        $description = $paymentMethod->name .' balance is changed from '. number_format($old_amount,3) .' to '. number_format($amount,3) . ' . Reason: ' . $reason;
        $user->payment_methods()->wherePivot('paymentmethod_id', $paymentMethod->id)->updateExistingPivot($paymentMethod->id, ['amount' => $amount]);
        // save log here
        activity('User Balance')
            ->performedOn($paymentMethod)
            ->causedBy($user)
            ->withProperties([
                'action' => 'update',
                'old_amount' => $old_amount,
                'new_amount' => $amount,
                'ip' => $userIP,
            ])
            ->log($description);
        return true;
    }
}
