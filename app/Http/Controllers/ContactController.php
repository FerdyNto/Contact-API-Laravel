<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    private function getContact(User $user, $id): Contact
    {
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
        return $contact;
    }
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
        $contact = $this->getContact($user, $id);

        return new ContactResource($contact);
    }

    public function update($id, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();

        // get contact berdasarkan user id
        $contact = $this->getContact($user, $id);

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete($id): JsonResponse
    {
        $user = Auth::user();

        // get contact berdasarkan user id
        $contact = $this->getContact($user, $id);

        $contact->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function search(Request $request): ContactCollection
    {
        $user = Auth::user();
        // set halaman
        $page = $request->input('page', 1);
        // set ukuran per halaman
        $size = $request->input('size', 10);

        // tampilkan data berdasarkan user id yang sedang login
        $contacts = Contact::query()->where('user_id', $user->id);

        // AND tampilkan data berdasarkan nama dengan builder
        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like', '%' . $name . '%');
                    $builder->orWhere('last_name', 'like', '%' . $name . '%');
                });
            }
            // AND tampilkan data email
            $email = $request->input('email');
            if ($email) {
                $builder->where('email', 'like', '%' . $email . '%');
            }
            // AND tampilkan data phone
            $phone = $request->input('phone');
            if ($email) {
                $builder->where('phone', 'like', '%' . $phone . '%');
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return new ContactCollection($contacts);
    }
}
