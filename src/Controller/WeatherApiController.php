<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * Class WeatherApiController
 * @package App\Controller
 * @SWG\Tag(name="Weather")
 * @author Vincent BESSEAU
 */
class WeatherApiController extends NetatmoController
{
    CONST SCOPE = "read_station";

    /**
     * @Route(name="netatmo_getstationsdata", path="/netatmo/weather/getstationsdata", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/weather/getstationsdata")
     * @SWG\Response(
     *     response=200,
     *     description="Returns data from a user Weather Stations (measures and device specific data).",
     * )
     * @SWG\Parameter(
     *     name="device_id",
     *     description="Weather station mac address",
     *     in="query",
     *     default=null,
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="get_favorites",
     *     description="To retrieve user's favorite weather stations. ",
     *     in="query",
     *     default="false",
     *     required=false,
     *     type="string"
     * )
     */
    public function getStationsData(Request $request) {
        return $this->client->post(self::API.'getstationsdata', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                'device_id' => $request->query->get('device_id') ?: null,
                'get_favorites' => $request->query->get('get_favorites') ?: false,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_getpublicdata", path="/netatmo/weather/getpublicdata", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/resources/technical/reference/weatherapi/getpublicdata")
     * @SWG\Response(
     *     response=200,
     *     description="Retrieves publicly shared weather data from Outdoor Modules within a predefined area.",
     * )
     * @SWG\Parameter(
     *     name="lat_ne",
     *     description="latitude of the north east corner of the requested area. -85 <= lat_ne <= 85 and lat_ne>lat_sw",
     *     in="query",
     *     required=true,
     *     type="number"
     * )
     * @SWG\Parameter(
     *     name="lon_ne",
     *     description="Longitude of the north east corner of the requested area. -180 <= lon_ne <= 180 and lon_ne>lon_sw",
     *     in="query",
     *     required=true,
     *     type="number"
     * )
     * @SWG\Parameter(
     *     name="lat_sw",
     *     description="latitude of the south west corner of the requested area. -85 <= lat_sw <= 85",
     *     in="query",
     *     required=true,
     *     type="number"
     * )
     * @SWG\Parameter(
     *     name="lon_sw",
     *     description="Longitude of the south west corner of the requested area. -180 <= lon_sw <= 180",
     *     in="query",
     *     required=true,
     *     type="number"
     * )
     * @SWG\Parameter(
     *     name="required_data",
     *     description="To filter stations based on relevant measurements you want (e.g. rain will only return stations with rain gauges).",
     *     in="query",
     *     required=false,
     *     default=null,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="filter",
     *     description="True to exclude station with abnormal temperature measures.",
     *     in="query",
     *     required=false,
     *     default="false",
     *     type="string"
     * )
     */
    public function getPublicData(Request $request) {
        return $this->client->post(self::API.'getpublicdata', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                'lat_ne' => $request->query->get('lat_ne'),
                'lon_ne' => $request->query->get('lon_ne'),
                'lat_sw' => $request->query->get('lat_sw'),
                'lon_sw' => $request->query->get('lon_sw'),
                'required_data' => $request->query->get('required_data') ?: null,
                'filter' => $request->query->get('filter') ?: false,
            ]
        ]);
    }
}