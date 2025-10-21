<?php

namespace App\Services;

use App\Models\Contact;
use Exception;
use Illuminate\Support\Facades\Auth;

class ContactService
{
    public function addContact($request)
    {
        try {
            Contact::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'phone' => $request->phone
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000')
                throw new Exception("This contact is already saved to your list!", 403);
            throw $e;
        }
    }

    public function getAllContacts($request)
    {
        try {
            return $request->search ?
                Contact::searchByPhoneOrName(Auth::id(), $request->search)
                ->select('id', 'name', 'phone')
                ->cursor()
                : Contact::where('user_id', Auth::id())
                ->select('id', 'name', 'phone')
                ->cursor();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateContact($request)
    {
        try {
            $contact = Contact::where('user_id', Auth::id())
                ->where('phone', $request->phone)
                ->first();

            if (!$contact)
                throw new Exception("This contact has not been found in our system.");

            $contact->update(['name' => $request->name, 'phone' => $request->phone]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function deleteContact($request)
    {
        try {
            $contact = Contact::where('user_id', Auth::id())
                ->where('phone', $request->phone)
                ->first();

            if (!$contact)
                throw new Exception("This contact has not been found in our system.");

            $contact->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
