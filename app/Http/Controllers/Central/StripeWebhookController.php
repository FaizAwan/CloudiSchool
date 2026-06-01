<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class StripeWebhookController extends CashierWebhookController
{
    protected function handleInvoicePaymentFailed(array $payload)
    {
        $stripeCustomer = $payload['data']['object']['customer'] ?? null;
        if (!$stripeCustomer) return $this->successMethod();

        $school = School::where('stripe_id', $stripeCustomer)->first();
        if ($school) {
            $school->update(['status' => 'suspended']);
            $this->setTenantSuspendedFlag($school->tenant_id, true);
        }

        return $this->successMethod();
    }

    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        $stripeCustomer = $payload['data']['object']['customer'] ?? null;
        if (!$stripeCustomer) return $this->successMethod();

        $school = School::where('stripe_id', $stripeCustomer)->first();
        if ($school) {
            $school->update(['status' => 'active']);
            $this->setTenantSuspendedFlag($school->tenant_id, false);
        }

        return $this->successMethod();
    }

    protected function setTenantSuspendedFlag(?string $tenantId, bool $suspend): void
    {
        if (!$tenantId) return;
        $tenant = Tenant::find($tenantId);
        if (!$tenant) return;
        $tenant->put('suspended', $suspend);
    }
}
