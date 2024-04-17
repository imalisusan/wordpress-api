<?php

declare(strict_types=1);

namespace App\Traits;

use App\Helpers\CraydelInternalResponseHelper;
use App\Helpers\CraydelJSONResponseHelper;
use Exception;
use Illuminate\Http\JsonResponse;

trait CanRespond
{
    use CanLog;

    /**
     * Respond to the requesting service
     *
     * @param CraydelJSONResponseHelper $response
     *
     * @return JsonResponse
     */
    public function respondInJSON(CraydelJSONResponseHelper $response): JsonResponse
    {
        return response()->json([
            'status' => $response->isStatus(),
            'message' => $response->getMessage(),
            'data' => $response->getData()
        ]);
    }

    /**
     * Respond to the requesting service with loginToken
     *
     * @param CraydelJSONResponseHelper $response
     *
     * @return JsonResponse
     */
    public function respondInJSONWithLoginToken(CraydelJSONResponseHelper $response): JsonResponse
    {
        return response()->json([
            'status' => $response->isStatus(),
            'message' => $response->getMessage(),
            '_token' => $response->getAuthenticationToken()
        ]);
    }

    /**
     * Respond as an internal response
     *
     * @param CraydelInternalResponseHelper $craydelInternalResponseHelper
     *
     * @return CraydelInternalResponseHelper
     */
    public function internalResponse(CraydelInternalResponseHelper $craydelInternalResponseHelper): CraydelInternalResponseHelper
    {
        if ($craydelInternalResponseHelper->exception instanceof Exception) {
            $this->logException($craydelInternalResponseHelper->exception);
        }

        return $craydelInternalResponseHelper;
    }
}
