<?php

namespace Kentron\Service;

use Kentron\Service\Curl;

use Kentron\Entity\Request\RequestEntity;

final class Request
{
    /**
     * Make the curl request using the info provided by the entity
     *
     * @param RequestEntity $restEntity
     *
     * @return bool The success of the request
     */
    public function run (RequestEntity $restEntity): bool
    {
        $curl = new Curl();

        $curl->setUrl($restEntity->getUrl());
        $curl->setHeaders($restEntity->getHeaders());

        if ($restEntity->isPost()) {
            $curl->setPost($restEntity->getPostData());
        }

        $curl->exec();

        if ($curl->hasFailed()) {
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
