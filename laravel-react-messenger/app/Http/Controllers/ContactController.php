<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ApiResponseTrait;
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index(Request $request)
    {
        try {
            $data = $this->contactService->getAllContacts($request);
            return $this->successResponse($data, 'Your contacts list retrieved successfully.', 200);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse($e->getMessage(), $statusCode);
        }
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'string|max:255',
            'phone' => 'required|max:15'
        ]);

        try {
            $this->contactService->addContact($request);
            return $this->successResponse([], 'This user phone has been added to your contacts list successfully.', 201);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse($e->getMessage(), $statusCode);
        }
    }

    public function updateContact(Request $request)
    {
        $request->validate([
            'name' => 'string|max:255',
            'phone' => 'required|max:15'
        ]);

        try {
            $this->contactService->updateContact($request);
            return $this->successResponse([], 'This contact has been updated successfully.', 200);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse($e->getMessage(), $statusCode);
        }
    }

    public function deleteContact(Request $request)
    {
        $request->validate(['phone' => 'required|max:15']);

        try {
            $this->contactService->deleteContact($request);
            return $this->successResponse([], 'This contact has been deleted from your list successfully.', 200);
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse($e->getMessage(), $statusCode);
        }
    }
}
