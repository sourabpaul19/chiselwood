<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        
        /* ========= GENERAL ========= */
        Setting::set('site_name', $request->site_name, 'general');
        Setting::set('admin_email', $request->admin_email, 'general');

        Setting::set('company_name', $request->company_name, 'general');
        Setting::set('company_address', $request->company_address, 'general');
        Setting::set('company_state', $request->company_state, 'general');
        Setting::set('pincode', $request->pincode, 'general');

        /* ========= TAX ========= */
        Setting::set('gst_rate', $request->gst_rate, 'tax');
        Setting::set('gstin', strtoupper($request->gstin), 'tax');
        Setting::set('cess', $request->cess, 'tax');

        /* ========= EMAIL ========= */
        Setting::set('smtp_host', $request->smtp_host, 'email');
        Setting::set('smtp_port', $request->smtp_port, 'email');
        Setting::set('smtp_username', $request->smtp_username, 'email');
        Setting::set('smtp_password', $request->smtp_password, 'email');

        /* ========= BRANDING (FILES) ========= */
        if ($request->hasFile('logo')) {

            $oldLogo = Setting::get('logo');

            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo')->store('settings/branding', 'public');
            Setting::set('logo', $path, 'branding');
        }

        /* ========= FAVICON ========= */
        if ($request->hasFile('favicon')) {

            $oldFavicon = Setting::get('favicon');

            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            $path = $request->file('favicon')->store('settings/branding', 'public');
            Setting::set('favicon', $path, 'branding');
        }
        /* ========= FAVICON ========= */
        if ($request->hasFile('signatory')) {

            $oldSignatory = Setting::get('signatory');

            if ($oldSignatory && Storage::disk('public')->exists($oldSignatory)) {
                Storage::disk('public')->delete($oldSignatory);
            }

            $path = $request->file('signatory')->store('settings/branding', 'public');
            Setting::set('signatory', $path, 'branding');
        }

        /* ========= INVOICE ========= */
        Setting::set('invoice_prefix', $request->invoice_prefix, 'invoice');
        Setting::set('invoice_footer', $request->invoice_footer, 'invoice');

        return back()->with('success', 'Settings updated successfully');
    }
}
