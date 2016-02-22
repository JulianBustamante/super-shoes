<?php

namespace SuperShoesBundle\Utils;

use Symfony\Component\HttpFoundation\Response;

class WebService {

    public function processResponse($entities, $entity_type) {
        $statusCode = 200;
        $success = true;

        if (null === $entities) {
            $statusCode = Response::HTTP_NOT_FOUND;
            $data['error_code'] = $statusCode;
            $data['error_message'] = Response::$statusTexts[$statusCode];
            $success = false;
        }
        else {
            $data = array($entity_type => $entities);
        }

        $data += array(
            'success' => $success,
            'status_code' => $statusCode,
        );

        return $data;
    }
}
