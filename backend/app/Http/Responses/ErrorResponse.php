<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ErrorResponse extends JsonResponse
{
    public function __construct(
        string $errorTrigger,
        string $code,
        string $message,
        int $status = 400,
        string $mainMessage = 'The given data was invalid.'
    ) {
        $data = [
            'message' => $mainMessage,
            'errors' => [
                $errorTrigger => [
                    [
                        'code' => $code,
                        'message' => $message,
                    ],
                ],
            ],
        ];

        parent::__construct($data, $status);
    }
}