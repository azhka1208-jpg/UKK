<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingsUpdateRequest;
use App\Models\Setting;
use App\Services\ActivityLogger;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::orderBy('group')->orderBy('id')->get()->keyBy('key');

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(SettingsUpdateRequest $request)
    {
        $data = $request->validated();

        $values = [
            'app_name' => $data['app_name'],
            'app_tagline' => $data['app_tagline'] ?? '',
            'app_description' => $data['app_description'] ?? '',
            'contact_email' => $data['contact_email'] ?? '',
            'contact_phone' => $data['contact_phone'] ?? '',
            'contact_address' => $data['contact_address'] ?? '',
            'fine_enabled' => $request->boolean('fine_enabled') ? '1' : '0',
            'fine_per_day' => (string) $data['fine_per_day'],
            'max_loan_days' => (string) $data['max_loan_days'],
            'announcement_title' => $data['announcement_title'] ?? '',
            'announcement_content' => $data['announcement_content'] ?? '',
            'announcement_active' => $request->boolean('announcement_active') ? '1' : '0',
        ];

        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        ActivityLogger::log('update', 'Memperbarui pengaturan sistem.');

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}