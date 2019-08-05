<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * Class EnergyApiController
 * @package App\Controller
 * @SWG\Tag(name="Energy")
 * @author Vincent BESSEAU
 */
class EnergyApiController extends NetatmoController
{
    CONST SCOPE = "read_thermostat write_thermostat";

    /**
     * @Route(name="netatmo_homesdata", path="/netatmo/energy/homesdata", defaults={ "_format" = "json" }, methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/energy/homesdata")
     * @SWG\Response(
     *     response=200,
     *     description="Retrieve data from a device or module (Weather station and Thermostat only).",
     * )
     */
    public function homesData() {
        return $this->client->post(self::API.'homesdata', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_homestatus", path="/netatmo/energy/homestatus/{homeId}", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @param $homeId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/energy/homestatus")
     * @SWG\Response(
     *     response=200,
     *     description="Get the current status and data measured for all home devices.",
     * )
     * @SWG\Parameter(
     *     name="homeId",
     *     description="id of home",
     *     in="path",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="device_types",
     *     description="Array of device type",
     *     in="query",
     *     default=null,
     *     required=false,
     *     type="string"
     * )
     */
    public function homeStatus(Request $request, $homeId) {
        return $this->client->post(self::API.'homestatus', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $homeId,
                'device_types' => $request->query->get('device_type') ?: null,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_setthermmode", path="/netatmo/energy/setthermmode", defaults={ "_format" = "json" }, methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/energy/setthermmode")
     * @SWG\Response(
     *     response=200,
     *     description="Set the home heating system to use schedule/ away/ frost guard mode.",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     description="json order object",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         required={"home_id","mode"},
     *         @SWG\Property(
     *              type="string",
     *              property="home_id",
     *              example="124335425425"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              enum={"schedule","away","hg"},
     *              property="mode",
     *              example="hg"
     *         ),
     *         @SWG\Property(
     *              type="number",
     *              property="endtime",
     *              example="null",
     *              default=null
     *         )
     *     )
     * )
     */
    public function setThermMode(Request $request) {
        $data = json_decode($request->getContent());
        return $this->client->post(self::API.'setthermmode', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $data->home_id,
                'mode' => $data->mode,
                'endtime' => (int)$data->endtime ?: null,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_setroomthermpoint", path="/netatmo/energy/setroomthermpoint", defaults={ "_format" = "json" }, methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/energy/setroomthermpoint")
     * @SWG\Response(
     *     response=200,
     *     description="Set a manual temperature to a room. or switch back to home mode",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     description="json order object",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         required={"home_id","room_id","mode"},
     *         @SWG\Property(
     *              type="string",
     *              property="home_id",
     *              example="124335425425"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="room_id",
     *              example="421341ar141"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              enum={"manual","home"},
     *              property="mode",
     *              example="manual"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="temp",
     *              default="null"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="endtime",
     *              default="null"
     *         )
     *     )
     * )
     */
    public function setRoomThermPoint(Request $request) {
        $data = json_decode($request->getContent());
        $endTime = ($data->endtime === "null" ? null : $data->endtime);
        $temp = ($data->temp === "null" ? null : $data->temp);
        return $this->client->post(self::API.'setroomthermpoint', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $data->home_id,
                'room_id' => $data->room_id,
                'mode' => $data->mode,
                'temp' => $temp === null ? null : (int)$temp,
                'endtime' => $endTime === null ? null : (int)$endTime,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_getroommeasure", path="/netatmo/energy/getroommeasure/{homeId}/{roomId}", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @param $homeId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/energy/getroommeasure")
     * @SWG\Response(
     *     response=200,
     *     description="Get the current status and data measured for all home devices.",
     * )
     * @SWG\Parameter(
     *     name="homeId",
     *     description="id of home",
     *     in="path",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="roomId",
     *     description="id of room",
     *     in="path",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="scale",
     *     description="step between measurements",
     *     enum={"max","30min","1hour","3hour","1day","1week","1month"},
     *     in="query",
     *     default="max",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="type",
     *     description="Measures you are interested in. Data you can request depends on the scale. See full details",
     *     enum={"Temperature","sp_temperature","min_temp","max_temp","date_min_temp"},
     *     in="query",
     *     default="Temperature",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="date_begin",
     *     description="Timestamp of the first measure to retrieve.",
     *     in="query",
     *     default=null,
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="date_end",
     *     description="Timestamp of the last measure to retrieve.",
     *     in="query",
     *     default=null,
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
    public function getRoomMeasure(Request $request, $homeId, $roomId) {
        return $this->client->post(self::API.'getroommeasure', [
            self::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $homeId,
                'room_id' => $roomId,
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
     * @Route(name="netatmo_switchhomeschedule", path="/netatmo/energy/switchhomeschedule", defaults={ "_format" = "json" }, methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/energy/switchhomeschedule")
     * @SWG\Response(
     *     response=200,
     *     description="Apply a specific schedule",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     description="json order object",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         required={"home_id","schedule_id"},
     *         @SWG\Property(
     *              type="string",
     *              property="home_id",
     *              example="124335425425"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="schedule_id",
     *              example="421341ar141"
     *         )
     *     )
     * )
     */
    public function switchHomeSchedule(Request $request) {
        $data = json_decode($request->getContent());
        return $this->client->post(self::API.'switchhomeschedule', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $data->home_id,
                NetatmoController::SCHEDULE_ID => $data->schedule_id,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_synchomeschedule", path="/netatmo/energy/synchomeschedule", defaults={ "_format" = "json" }, methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/energy/synchomeschedule")
     * @SWG\Response(
     *     response=200,
     *     description="Changes the Thermostat weekly schedule.",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     description="json order object",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         required={"device_id","module_id","zones","timetable","hg_temp","away_temp"},
     *         @SWG\Property(
     *              type="string",
     *              property="device_id",
     *              example="70:ee:50:00:00:xx"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="module_id",
     *              example="70:ee:50:00:00:xx"
     *         ),
     *         @SWG\Property(
     *              type="object",
     *              property="zones",
     *              example={{"type": 0,"name": "confort","temp": 19,"id": 0},{"type": 1,"name": "nuit","temp": 18,"id": 1}},
     *              @SWG\Schema(
     *                  type="object",
     *                  @SWG\Property(
     *                      type="number",
     *                      property="type",
     *                      example=1
     *                  ),
     *                  @SWG\Property(
     *                      type="string",
     *                      property="name",
     *                      example="confort"
     *                  ),
     *                  @SWG\Property(
     *                      type="number",
     *                      property="temp",
     *                      example=19
     *                  ),
     *                  @SWG\Property(
     *                      type="number",
     *                      property="id",
     *                      example=0
     *                  )
     *              )
     *         ),
     *         @SWG\Property(
     *              type="object",
     *              property="timetable",
     *              example={{"id": 1,"m_offset": 0},{"id": 0,"m_offset": 1140}},
     *              @SWG\Schema(
     *                  type="object",
     *                  @SWG\Property(
     *                      type="number",
     *                      property="id",
     *                      example=1
     *                  ),
     *                  @SWG\Property(
     *                      type="number",
     *                      property="m_offset",
     *                      example=0
     *                  )
     *              )
     *         ),
     *         @SWG\Property(
     *              type="number",
     *              property="hg_temp",
     *              example="8"
     *         ),
     *         @SWG\Property(
     *              type="number",
     *              property="away_temp",
     *              example="15"
     *         ),
     *     )
     * )
     */
    public function synchomeSchedule(Request $request) {
        $data = json_decode($request->getContent());
        return $this->client->post(self::API.'synchomeschedule', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                'device_id' => $data->device_id,
                'module_id' => $data->module_id,
                'zones' => $data->zones,
                'timetable' => $data->timetable,
                'hg_temp' => $data->hg_temp,
                'away_temp' => $data->away_temp,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_renamehomeschedule", path="/netatmo/energy/renamehomeschedule", defaults={ "_format" = "json" }, methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/energy/renamehomeschedule")
     * @SWG\Response(
     *     response=200,
     *     description="Update the given schedule name",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     description="json order object",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         required={"home_id","schedule_id"},
     *         @SWG\Property(
     *              type="string",
     *              property="home_id",
     *              example="124335425425"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="schedule_id",
     *              example="421341ar141"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="name",
     *              example="toto"
     *         )
     *     )
     * )
     */
    public function renameHomeSchedule(Request $request) {
        $data = json_decode($request->getContent());
        return $this->client->post(self::API.'renamehomeschedule', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $data->home_id,
                NetatmoController::SCHEDULE_ID => $data->schedule_id,
                'name' => $data->name,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_deletehomeschedule", path="/netatmo/energy/deletehomeschedule", defaults={ "_format" = "json" }, methods={"DELETE"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/energy/deletehomeschedule")
     * @SWG\Response(
     *     response=200,
     *     description="Delete the given schedule",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     description="json order object",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         required={"home_id","schedule_id"},
     *         @SWG\Property(
     *              type="string",
     *              property="home_id",
     *              example="124335425425"
     *         ),
     *         @SWG\Property(
     *              type="object",
     *              property="zones",
     *              example={{"type": 0,"name": "confort","temp": 19,"id": 0},{"type": 1,"name": "nuit","temp": 18,"id": 1}},
     *              @SWG\Schema(
     *                  type="object",
     *                  @SWG\Property(
     *                      type="number",
     *                      property="type",
     *                      example=1
     *                  ),
     *                  @SWG\Property(
     *                      type="string",
     *                      property="name",
     *                      example="confort"
     *                  ),
     *                  @SWG\Property(
     *                      type="number",
     *                      property="temp",
     *                      example=19
     *                  ),
     *                  @SWG\Property(
     *                      type="number",
     *                      property="id",
     *                      example=0
     *                  )
     *              )
     *         ),
     *         @SWG\Property(
     *              type="object",
     *              property="timetable",
     *              example={{"id": 1,"m_offset": 0},{"id": 0,"m_offset": 1140}},
     *              @SWG\Schema(
     *                  type="object",
     *                  @SWG\Property(
     *                      type="number",
     *                      property="id",
     *                      example=1
     *                  ),
     *                  @SWG\Property(
     *                      type="number",
     *                      property="m_offset",
     *                      example=0
     *                  )
     *              )
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="name",
     *              example="toto"
     *         )
     *     )
     * )
     */
    public function deleteHomeSchedule(Request $request) {
        $data = json_decode($request->getContent());
        return $this->client->post(self::API.'deletehomeschedule', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $data->home_id,
                NetatmoController::SCHEDULE_ID => $data->schedule_id,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_createnewhomeschedule", path="/netatmo/energy/createnewhomeschedule", defaults={ "_format" = "json" }, methods={"PUT"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/energy/createnewhomeschedule")
     * @SWG\Response(
     *     response=200,
     *     description="Delete the given schedule",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     description="json order object",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         required={"home_id","schedule_id"},
     *         @SWG\Property(
     *              type="string",
     *              property="home_id",
     *              example="124335425425"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="schedule_id",
     *              example="421341ar141"
     *         ),
     *         @SWG\Property(
     *              type="number",
     *              property="hg_temp",
     *              example="8"
     *         ),
     *         @SWG\Property(
     *              type="number",
     *              property="away_temp",
     *              example="15"
     *         )
     *     )
     * )
     */
    public function createNewHomeSchedule(Request $request) {
        $data = json_decode($request->getContent());
        return $this->client->post(self::API.'createnewhomeschedule', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $data->home_id,
                'timetable' => $data->timetable,
                'zones' => $data->zones,
                'name' => $data->name,
                'hg_temp' => $data->hg_temp,
                'away_temp' => $data->away_temp,
            ]
        ]);
    }
}