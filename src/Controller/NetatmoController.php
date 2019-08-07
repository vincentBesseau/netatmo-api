<?php

namespace App\Controller;


use App\Service\ClientRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * Class NetatmoController
 * @package App\Controller
 */
class NetatmoController extends AbstractController
{
    CONST BASE_URI = 'https://api.netatmo.com/';
    CONST OAUT_TOKEN = 'oauth2/token';
    CONST API = 'api/';
    CONST ACCESS_TOKEN = 'access_token';
    CONST DEFAULT_HEADER = ['Content-type' => 'application/x-www-form-urlencoded'];
    const FORM_PARAMS = 'form_params';
    const HOME_ID = 'home_id';
    const SCHEDULE_ID = 'schedule_id';
    const PERSON_ID = 'person_id';
    const EVENT_ID = 'event_id';

    protected $client;
    protected $token;

    /**
     * NetatmoController constructor.
     */
    public function __construct(ClientRequestService $client)
    {
        $clientService = $client;
        $this->client = $clientService->setting([
            'base_url' => self::BASE_URI,
            'header' => self::DEFAULT_HEADER,
        ]);

        $this->client->setToken(self::OAUT_TOKEN, [
            self::FORM_PARAMS => [
                'client_id' => '',
                'client_secret' => '',
                'username' => '',
                'password' => '',
                'grant_type' => 'password',
                'scope' => !defined('static::SCOPE') ? '' : static::SCOPE
            ],
        ]);

        $this->token = json_decode($this->client->getToken()->getContent())->message->access_token;
    }

    /**
     * @Route(name="netatmo_token", path="/netatmo/token", defaults={ "_format" = "json" }, methods={"GET"})
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @SWG\Tag(name="Authentication")
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/guides/authentication/clientcredentials")
     * @SWG\Response(
     *     response=200,
     *     description="Return the access token",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *              @SWG\Property(property="access_token", type="string", description="Access token for your user"),
     *              @SWG\Property(property="expires_in", type="number", description="Validity timelaps in seconds"),
     *              @SWG\Property(property="refresh_token", type="string", description="Use this token to get a new access_token once it has expired")
     *         )
     *     )
     * )
     */
    public function getToken() {
        return $this->client->getToken();
    }

    /**
     * @Route(name="netatmo_getmeasure", path="/netatmo/getmeasure/{deviceId}/{moduleId}", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @param $deviceId
     * @param $moduleId
     * @return JsonResponse
     * @SWG\Tag(name="Common")
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/common/getmeasure")
     * @SWG\Response(
     *     response=200,
     *     description="Retrieve data from a device or module (Weather station and Thermostat only).",
     * )
     * @SWG\Parameter(
     *     name="deviceId",
     *     description="Mac address of the device (can be found via getuser)",
     *     in="path",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="moduleId",
     *     description="Mac address of the module you’re interested in.",
     *     in="path",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="scale",
     *     description="Timelapse between two measurements",
     *     enum={"max","30min","1hour","3hour","1day","1week","1month"},
     *     in="query",
     *     default="max",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="type",
     *     description="Measures you are interested in. Data you can request depends on the scale. See full details",
     *     enum={"Temperature","CO2","Humidity","Pressure","Noise","Rain","WindStrength","WindAngle","Guststrength","GustAngle","min_temp","max_temp","min_hum","max_hum","WindStrength","max_pressure","min_noise","max_noise","sum_rain","date_max_gust","date_max_hum","min_pressure","date_min_pressure","date_max_pressure","date_min_noise","date_max_noise","date_min_co2","date_max_co2","sp_temperature","boileron","boileroff","sum_boiler_on","sum_boiler_off","date_min_temp"},
     *     in="query",
     *     default="Temperature",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="date_begin",
     *     description="Timestamp of the first measure to retrieve.",
     *     in="query",
     *     default="null",
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="date_end",
     *     description="Timestamp of the last measure to retrieve.",
     *     in="query",
     *     default="null",
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     description="Maximum number of measurements.",
     *     in="query",
     *     default="1024",
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="optimize",
     *     description="Determines the format of the answer. Default is true. For mobile apps we recommend True and False if bandwidth isn't an issue as it is easier to parse.",
     *     in="query",
     *     default="true",
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="real_time",
     *     description="If scale different than max, timestamps are by default offset + scale/2. To get exact timestamps, use true.",
     *     in="query",
     *     default="false",
     *     required=false,
     *     type="string"
     * )
     */
    public function getMeasure(Request $request, $deviceId, $moduleId) {
        return $this->client->post(self::API.'getmeasure', [
            self::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                'device_id' => $deviceId,
                'module_id' => $moduleId,
                'scale' => $request->query->get('scale') ?: "max",
                'type' => $request->query->get('type') ?: 'Temperature',
                'date_begin' => $request->query->get('date_begin') ?: null,
                'date_end' => $request->query->get('date_end') ?: null,
                'limit' => $request->query->get('limit') ?: 1024,
                'optimize' => $request->query->get('optimize') ?: true,
                'real_time' => $request->query->get('real_time') ?: false,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_getuser", path="/netatmo/getuser", defaults={ "_format" = "json" }, methods={"GET"})
     * @SWG\Tag(name="Common")
     * @SWG\Response(
     *     response=200,
     *     description="Retrieve users.",
     * )
     * @return mixed|JsonResponse
     */
    public function getUser()
    {
        return $this->client->post(self::API.'getuser', [
            self::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
            ]
        ]);
    }
}