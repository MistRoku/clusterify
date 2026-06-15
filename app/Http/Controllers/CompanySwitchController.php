<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanySwitchController extends Controller
{
    /**
     * Switch the current working company for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request)
    {
        $companyId = $request->input('company_id');

        // Only super admins can switch companies
        if (!Auth::user()->isGlobalSuperAdmin()) {
            abort(403, 'Only super administrators can switch companies.');
        }

        // Validate company exists and is active
        $company = Company::where('id', $companyId)
                          ->where('is_active', true)
                          ->firstOrFail();

        // Store the selected company ID in the session
        session(['current_company_id' => $company->id]);

        return redirect()->back()->with('success', 'Switched to company: ' . $company->name);
    }

    /**
     * Reset back to global super admin view (no current company).
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset()
    {
        session()->forget('current_company_id');
        return redirect()->back()->with('success', 'Switched back to global view.');
    }
}
