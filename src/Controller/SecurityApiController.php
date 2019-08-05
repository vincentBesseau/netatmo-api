<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * Class SecurityApiController
 * @package App\Controller
 * @SWG\Tag(name="Security")
 * @author Vincent BESSEAU
 */
class SecurityApiController extends NetatmoController
{
    CONST SCOPE = "read_camera acces_camera read_presence access_presence read_smokedetector write_camera";

    /**
     * @Route(name="netatmo_getcamerapicture", path="/netatmo/security/getcamerapicture", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/security/getcamerapicture")
     * @SWG\Response(
     *     response=200,
     *     description="Returns data from a user's Healthy Home Coach (measures and device specific data)",
     * )
     * @SWG\Parameter(
     *     name="image_id",
     *     description="id of the image",
     *     in="query",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="key",
     *     description="Security key to access snapshots",
     *     in="query",
     *     required=true,
     *     type="string"
     * )
     */
    public function getCameraPicture(Request $request) {
        return $this->client->post(self::API.'getcamerapicture', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                'image_id' => $request->query->get('image_id'),
                'key' => $request->query->get('key'),
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_geteventsuntil", path="/netatmo/security/geteventsuntil", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/security/geteventsuntil")
     * @SWG\Response(
     *     response=200,
     *     description="Returns all the events until the one specified in the request.",
     * )
     * @SWG\Parameter(
     *     name="home_id",
     *     description="ID of the Home you're interested in",
     *     in="query",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="event_id",
     *     description="Your request will retrieve all the events until this one",
     *     in="query",
     *     required=true,
     *     type="string"
     * )
     */
    public function getEventsUntil(Request $request) {
        return $this->client->post(self::API.'geteventsuntil', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $request->query->get(NetatmoController::HOME_ID ),
                NetatmoController::EVENT_ID  => $request->query->get(NetatmoController::EVENT_ID ),
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_gethomedata", path="/netatmo/security/gethomedata", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/security/gethomedata")
     * @SWG\Response(
     *     response=200,
     *     description="Returns information about users homes and cameras.",
     * )
     * @SWG\Parameter(
     *     name="home_id",
     *     description="Specify if you're looking for the events of a specific Home.",
     *     in="query",
     *     required=false,
     *     default=null,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="size",
     *     description="Number of events to retrieve.",
     *     in="query",
     *     required=false,
     *     default=30,
     *     type="string"
     * )
     */
    public function getHomeData(Request $request) {
        return $this->client->post(self::API.'gethomedata', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $request->query->get(NetatmoController::HOME_ID ) ?: null,
                'size' => $request->query->get('size') ?: 30,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_getlasteventof", path="/netatmo/security/getlasteventof", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/security/getlasteventof")
     * @SWG\Response(
     *     response=200,
     *     description="Returns most recent events.",
     * )
     * @SWG\Parameter(
     *     name="home_id",
     *     description="ID of the Home you're interested in",
     *     in="query",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="person_id",
     *     description="Your request will retrieve all events of the given home until the most recent event of the given person.",
     *     in="query",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="offset",
     *     description="Number of events to retrieve.",
     *     in="query",
     *     required=false,
     *     default=30,
     *     type="string"
     * )
     */
    public function getLastEventOf(Request $request) {
        return $this->client->post(self::API.'getlasteventof', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $request->query->get(NetatmoController::HOME_ID ),
                NetatmoController::PERSON_ID => $request->query->get('person_id'),
                'offset' => $request->query->get('offset') ?: 30,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_getnextevents", path="/netatmo/security/getnextevents", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/security/getnextevents")
     * @SWG\Response(
     *     response=200,
     *     description="Returns previous events.",
     * )
     * @SWG\Parameter(
     *     name="home_id",
     *     description="ID of the Home you're interested in",
     *     in="query",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="event_id",
     *     description="Your request will retrieve events occured before this one.",
     *     in="query",
     *     required=true,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="size",
     *     description="Number of event to retrieve.",
     *     in="query",
     *     required=false,
     *     default=30,
     *     type="string"
     * )
     */
    public function getNextEvents(Request $request) {
        return $this->client->post(self::API.'getnextevents', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $request->query->get(NetatmoController::HOME_ID ),
                NetatmoController::EVENT_ID  => $request->query->get(NetatmoController::EVENT_ID ),
                'size' => $request->query->get('size') ?: 30,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_setpersonsaway", path="/netatmo/security/setpersonsaway", defaults={ "_format" = "json" }, methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/security/setpersonsaway")
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
     *         required={"home_id"},
     *         @SWG\Property(
     *              type="string",
     *              property="home_id",
     *              example="121312Zv123a"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="person_id",
     *              example="1594XXXX-XXX-4XXX-bXXX+B13XX-3060a4XXXXXX"
     *         ),
     *     )
     * )
     */
    public function setPersonsAway(Request $request) {
        $data = json_decode($request->getContent());
        return $this->client->post(self::API.'setpersonsaway', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $data->home_id,
                NetatmoController::PERSON_ID => $data->person_id ?: null,
            ]
        ]);
    }

    /**
     * @Route(name="netatmo_setpersonshome", path="/netatmo/security/setpersonshome", defaults={ "_format" = "json" }, methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/en-US/resources/technical/reference/security/setpersonshome")
     * @SWG\Response(
     *     response=200,
     *     description="Sets a person or a group of person 'at home'. The event will be added to the user’s timeline.",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     description="json order object",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         required={"home_id","person_id"},
     *         @SWG\Property(
     *              type="string",
     *              property="home_id",
     *              example="121312Zv123a"
     *         ),
     *         @SWG\Property(
     *              type="string",
     *              property="person_id",
     *              example={"1594XXXX-XXX-4XXX-bXXX+B13XX-3060a4XXXXXX"}
     *         ),
     *     )
     * )
     */
    public function setPersonsHome(Request $request) {
        $data = json_decode($request->getContent());
        return $this->client->post(self::API.'setpersonshome', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
                NetatmoController::HOME_ID => $data->home_id,
                NetatmoController::PERSON_ID => $data->person_id,
            ]
        ]);
    }
}