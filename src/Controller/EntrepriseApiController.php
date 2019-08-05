<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * Class EntrepriseApiController
 * @package App\Controller
 * @SWG\Tag(name="Entreprise API")
 * @author Vincent BESSEAU
 */
class EntrepriseApiController extends NetatmoController
{
    const SCOPE = "";
    /**
     * @Route(name="netatmo_partnerdevices", path="/netatmo/entrepriseapi/partnerdevices", defaults={ "_format" = "json" }, methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\ExternalDocumentation(url="https://dev.netatmo.com/resources/technical/reference/enterpriseapi/partnerdevices")
     * @SWG\Response(
     *     response=200,
     *     description="Retrieve the list of device_id associated with your Enterprise application.",
     * )
     */
    public function partnerDevices() {
        return $this->client->post(self::API.'partnerdevices', [
            NetatmoController::FORM_PARAMS => [
                self::ACCESS_TOKEN => $this->token,
            ]
        ]);
    }
}