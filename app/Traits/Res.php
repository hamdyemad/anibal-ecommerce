<?php

namespace App\Traits;

trait Res
{
    public function sendRes($message, $status = true,  $data = [], $errors = [], $code = 200)
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'errors' => $errors,
        ];

        if (is_object($data) && $data->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $response['data'] = $data->items();
            $response['pagination'] = $this->getPaginationMeta($data);
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public function sendData($message, $status = true,  $data = [], $errors = [])
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ];
    }

    /**
     * Extract pagination metadata from LengthAwarePaginator
     *
     * @param mixed $data Collection or LengthAwarePaginator
     * @return array|null
     */
    public function getPaginationMeta($data)
    {
        return [
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
        ];
    }
}
