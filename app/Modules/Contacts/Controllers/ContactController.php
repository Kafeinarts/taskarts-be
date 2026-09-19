<?php

declare(strict_types=1);

namespace App\Modules\Contacts\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Contacts\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ContactController
 *
 * Kontroler RESTful untuk resource Contact pada modul Contacts.
 */
class ContactController extends ApiController
{
    /**
     * Daftar kontak milik user dengan filter opsional.
     */
    public function index(Request $request, ContactService $service): JsonResponse
    {
        $contacts = $service->index($request->query());

        return $this->ok($contacts, 'Daftar kontak');
    }

    /**
     * Detail satu kontak.
     */
    public function show(Contact $contact, ContactService $service): JsonResponse
    {
        return $this->ok($service->show($contact), 'Detail kontak');
    }

    /**
     * Buat kontak baru.
     */
    public function store(Request $request, ContactService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable'],
            'notes' => ['nullable', 'string'],
            'is_favorite' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ]);

        $contact = $service->store($data);

        return $this->ok($contact, 'Kontak dibuat', 201);
    }

    /**
     * Perbarui kontak yang sudah ada.
     */
    public function update(Request $request, Contact $contact, ContactService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable'],
            'notes' => ['nullable', 'string'],
            'is_favorite' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ]);

        $updated = $service->update($contact, $data);

        return $this->ok($updated, 'Kontak diperbarui');
    }

    /**
     * Hapus kontak.
     */
    public function destroy(Contact $contact, ContactService $service): JsonResponse
    {
        $service->destroy($contact);

        return $this->ok([], 'Kontak dihapus');
    }

    /**
     * Statistik kontak (total, favorit, perusahaan unik).
     */
    public function stats(ContactService $service): JsonResponse
    {
        return $this->ok($service->stats(), 'Statistik kontak');
    }
}
