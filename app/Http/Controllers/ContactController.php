<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\FlareClient\Api;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        // create Contact
        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get($id): ContactResource
    {
        // cek user yang sedang login
        $user = Auth::user();

        // get contact berdasarkan user id
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        // cek apakah ada kontak
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new ContactResource($contact);
    }

    public function update($id, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();

        // get contact berdasarkan user id
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        // cek apakah ada kontak
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete($id): JsonResponse
    {
        $user = Auth::user();

        // get contact berdasarkan user id
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        // cek apakah ada kontak
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $contact->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
