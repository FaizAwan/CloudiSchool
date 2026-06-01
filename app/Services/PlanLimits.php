<?php

namespace App\Services;

use App\Models\School;

class PlanLimits
{
    /**
     * Return an array of limits for the school's current plan.
     */
    public function forSchool(School $school): array
    {
        $plans = config('billing.plans', []);
        $defaults = config('billing.defaults', []);

        $subscription = $school->subscription('default');
        if (!$subscription) {
            return $defaults;
        }

        // Cashier stores the Stripe price on the subscription row (stripe_price)
        $priceId = $subscription->stripe_price ?? null;
        if ($priceId && isset($plans[$priceId])) {
            return $plans[$priceId];
        }

        return $defaults;
    }
}
