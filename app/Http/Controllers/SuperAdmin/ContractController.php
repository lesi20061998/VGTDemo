<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contract::query();

        if ($request->has('service_type') && $request->service_type != '') {
            $query->where('service_type', $request->service_type);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('superadmin.contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.contracts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'service_type' => 'required|in:website,publication,branding,social_media',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'domain_name' => 'nullable|string|max:255',
            'domain_purchase_date' => 'nullable|date',
            'hosting_provider' => 'nullable|string|max:255',
            'hosting_start_date' => 'nullable|date',
            'contract_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,active,completed,cancelled',
            'description' => 'nullable|string',
            'technical_requirements' => 'nullable|string',
            'features' => 'nullable|string',
            'has_client_resources' => 'boolean',
            'client_resource_details' => 'nullable|string',
            'attachment_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $validated['has_client_resources'] = $request->boolean('has_client_resources');

        if ($request->input('action') === 'approve') {
            $validated['status'] = 'active';
        }

        // Handle attachments
        $attachments = [];
        if ($request->hasFile('attachment_files')) {
            foreach ($request->file('attachment_files') as $file) {
                $path = $file->store('contracts', 'public');
                $attachments[] = $path;
            }
        }
        $validated['attachments'] = $attachments;

        Contract::create($validated);

        return redirect()->route('superadmin.contracts.index')->with('success', 'Hợp đồng đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        return view('superadmin.contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        return view('superadmin.contracts.edit', compact('contract'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'service_type' => 'required|in:website,publication,branding,social_media',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'domain_name' => 'nullable|string|max:255',
            'domain_purchase_date' => 'nullable|date',
            'hosting_provider' => 'nullable|string|max:255',
            'hosting_start_date' => 'nullable|date',
            'contract_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,active,completed,cancelled',
            'description' => 'nullable|string',
            'technical_requirements' => 'nullable|string',
            'features' => 'nullable|string',
            'has_client_resources' => 'boolean',
            'client_resource_details' => 'nullable|string',
            'attachment_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $validated['has_client_resources'] = $request->boolean('has_client_resources');

        if ($request->input('action') === 'approve') {
            $validated['status'] = 'active';
        }

        $attachments = $contract->attachments ?? [];

        // Remove old attachments if requested
        if ($request->has('remove_attachments')) {
            foreach ($request->remove_attachments as $removePath) {
                if (($key = array_search($removePath, $attachments)) !== false) {
                    unset($attachments[$key]);
                    Storage::disk('public')->delete($removePath);
                }
            }
            $attachments = array_values($attachments); // re-index
        }

        // Add new attachments
        if ($request->hasFile('attachment_files')) {
            foreach ($request->file('attachment_files') as $file) {
                $path = $file->store('contracts', 'public');
                $attachments[] = $path;
            }
        }
        $validated['attachments'] = $attachments;

        $contract->update($validated);

        return redirect()->route('superadmin.contracts.index')->with('success', 'Cập nhật hợp đồng thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        // Delete physical files
        if ($contract->attachments) {
            foreach ($contract->attachments as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $contract->delete();

        return redirect()->route('superadmin.contracts.index')->with('success', 'Xóa hợp đồng thành công.');
    }
}
