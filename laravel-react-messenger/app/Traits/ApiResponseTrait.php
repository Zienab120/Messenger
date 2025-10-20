<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function successResponse($data, $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $status);
    }
    public function errorResponse($message, $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null
        ], $status);
    }

    public function PaginatedResponse($data, $pagination, $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'pagination' => [
                    'total' => $pagination->total(),
                    'per_page' => $pagination->perPage(),
                    'current_page' => $pagination->currentPage(),
                    'last_page' => $pagination->lastPage(),
                    'next_page_url' => $pagination->nextPageUrl(),
                    'prev_page_url' => $pagination->previousPageUrl(),
                    'from' => $pagination->firstItem(),
                    'to' => $pagination->lastItem(),
                ],
            ]
        ], $status);
    }
}
