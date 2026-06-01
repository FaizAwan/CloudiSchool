<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Cashier\Checkout;

class BillingController extends Controller
{
    // Optional: show a simple plan page (can be skipped if you link straight to checkout)
    public function plans(School $school)
    {
        // For simplicity, we hardcode one plan id from Stripe: price_basic_monthly
        return view('central.tenants.plans', compact('school'));
    }

    // Create Stripe Checkout session and redirect
    public function checkout(Request $request, School $school)
    {
        $priceId = $request->input('price', config('services.stripe_basic_price', 'price_basic_monthly'));

        if (!$school->stripe_id) {
            $school->createOrGetStripeCustomer([
                'name' => $school->schoolName,
                'email' => $school->schoolAdminEmail,
            ]);
        }

        return $school->newSubscription('default', $priceId)->checkout([
            'success_url' => route('billing.success', $school),
            'cancel_url'  => route('billing.cancel', $school),
        ]);
    }

    public function success(School $school)
    {
        $school->refresh();
        return redirect()->route('saas.tenants.index')->with('status', 'Subscription active for '.$school->schoolName);
    }

    public function cancel(School $school)
    {
        return redirect()->route('saas.tenants.index')->with('error', 'Checkout cancelled for '.$school->schoolName);
    }

    // Stripe Billing Portal redirect
    public function portal(School $school)
    {
        if (!$school->stripe_id) {
            // No customer yet; send to plans/checkout
            return redirect()->route('billing.plans', $school)->with('error', 'No Stripe customer found. Choose a plan.');
        }
        return $school->redirectToBillingPortal(route('saas.tenants.index'));
    }
}
