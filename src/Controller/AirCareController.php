<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * Class AirCareController
 * @package App\Controller
 * @SWG\Tag(name="Aire Care")
 * @author Vincent BESSEAU
 */
class AirCareController extends NetatmoController
{
    CONST SCOPE = "read_homecoach";

    /**
     * @Route(name="netatmo_gethomecoachsdata", path="/netatmo/aircare/gethomecoachsdata", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/aircare/gethomecoachsdata")
     * @SWG\Response(
     *     response=200,
     *     description="Returns data from a user's Healthy Home Coach (measures and device specific data)",
     * )
     * @SWG\Parameter(
     *     name="device_id",
     *     description="Healthy Home Coach mac address",
     *     in="query",
     *     default=null,
     *     required=false,
     *     type="string"
     * )
     */
    public function getHomeCoachsData(Request $request) {
        return $this->client->post(self::API.'gethomecoachsdata', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                'device_id' => $request->query->get('device_id') ?: null,
            ]
        ]);
    }
}