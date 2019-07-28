<?php

    namespace Kentron\Proxy\Rest;

    use Kentron\Facade\Curl;

    use Kentron\Proxy\Rest\Entity\RestEntity;

    final class RestService
    {
        /**
         * Make the curl request
         * @param  RestEntity $restEntity The rest entity of all the information CURL needs
         * @return bool                   The success of the request
         */
        public function makeRequest (RestEntity $restEntity): bool
        {
            $curl = new Curl();

            $curl->setUrl($restEntity->getUrl());
            $curl->setHeaders(["X-AUTH-TOKEN: " . $restEntity->getApiKey()]);

            if ($restEntity->isPost()) {
                $curl->setPost($restEntity->getPostData());
            }

            if ($curl->execute()) {
                $restEntity->addError($curl->getErrors());
                return false;
            }

            $restEntity->parseResponse($curl->getResponse());

            if ($restEntity->hasErrors()) {
                return false;
            }

            return true;
        }
    }
