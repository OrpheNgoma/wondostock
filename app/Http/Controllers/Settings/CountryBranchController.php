<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CountryBranchController extends Controller
{
    public function create()
    {
        return view('settings.country-branch.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:3',
            'country_name' => 'required|string|max:255',
            'nif' => 'nullable|string|max:50',
            'rccm' => 'nullable|string|max:50',
            'business_permit' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'postal_box' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'header_image' => 'nullable|image|max:2048',
            'footer_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'contact_phone' => $request->contact_phone,
            'country_code' => $request->country_code,
            'country_name' => $request->country_name,
            'nif' => $request->nif,
            'rccm' => $request->rccm,
            'business_permit' => $request->business_permit,
            'tax_id' => $request->tax_id,
            'email' => $request->email,
            'website' => $request->website,
            'postal_box' => $request->postal_box,
            'is_active' => $request->boolean('is_active', true),
            'is_country_branch' => true,
        ];

        // Gestion de l'upload de l'image d'en-tête
        if ($request->hasFile('header_image')) {
            $data['invoice_header_image'] = $request->file('header_image')->store('invoice-headers', 'public');
        }

        // Gestion de l'upload de l'image de pied de page
        if ($request->hasFile('footer_image')) {
            $data['invoice_footer_image'] = $request->file('footer_image')->store('invoice-footers', 'public');
        }

        Store::create($data);

        return redirect()->route('stores.index')->with('success', 'Branche pays créée avec succès !');
    }

    public function edit(Store $store)
    {
        if (!$store->is_country_branch) {
            abort(404);
        }

        return view('settings.country-branch.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        if (!$store->is_country_branch) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:3',
            'country_name' => 'required|string|max:255',
            'nif' => 'nullable|string|max:50',
            'rccm' => 'nullable|string|max:50',
            'business_permit' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'postal_box' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'header_image' => 'nullable|image|max:2048',
            'footer_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'contact_phone' => $request->contact_phone,
            'country_code' => $request->country_code,
            'country_name' => $request->country_name,
            'nif' => $request->nif,
            'rccm' => $request->rccm,
            'business_permit' => $request->business_permit,
            'tax_id' => $request->tax_id,
            'email' => $request->email,
            'website' => $request->website,
            'postal_box' => $request->postal_box,
            'is_active' => $request->boolean('is_active', true),
        ];

        // Gestion de l'upload de l'image d'en-tête
        if ($request->hasFile('header_image')) {
            // Supprimer l'ancienne image si elle existe
            if ($store->invoice_header_image) {
                Storage::delete($store->invoice_header_image);
            }
            $data['invoice_header_image'] = $request->file('header_image')->store('invoice-headers', 'public');
        }

        // Gestion de l'upload de l'image de pied de page
        if ($request->hasFile('footer_image')) {
            // Supprimer l'ancienne image si elle existe
            if ($store->invoice_footer_image) {
                Storage::delete($store->invoice_footer_image);
            }
            $data['invoice_footer_image'] = $request->file('footer_image')->store('invoice-footers', 'public');
        }

        $store->update($data);

        return redirect()->route('stores.index')->with('success', 'Branche pays mise à jour avec succès !');
    }
}