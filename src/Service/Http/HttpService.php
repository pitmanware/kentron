<?php

namespace Kentron\Service\Http;

use Kentron\Facade\Curl;

use Kentron\Service\Type;
use Kentron\Service\Http\Entity\HttpEntity;

final class HttpService
{
    /**
     * Make the HTTP request
     *
     * @param HttpEntity $httpEntity The request entity
     *
     * @return bool The success of the request
     */
    public static function run (HttpEntity $httpEntity): bool
    {
        if ($httpEntity->isCurl())
        {
            return self::runCurl($httpEntity);
        }
        else
        {
            return self::runSoap($httpEntity);
        }
    }

    /**
     * Make the cURL request using the info provided by the entity
     *
     * @param HttpEntity $httpEntity
     *
     * @return bool
     */
    private static function runCurl (HttpEntity $httpEntity): bool
    {
        $curl = new Curl();

        $curl->setUrl($httpEntity->getUrl());
        $curl->setHeaders($httpEntity->getHeaders());

        if ($httpEntity->isPost())
        {
            $curl->setPost($httpEntity->getPostData());
        }

        $curl->setRequestMethod($httpEntity->getHttpMethod());

        if (!$curl->execute())
        {
            $httpEntity->addError($curl->getErrors());
            return false;
        }

        $httpEntity->setStatusCode($curl->getStatusCode());
        $httpEntity->parseResponse($curl->getResponse());

        if ($httpEntity->hasErrors())
        {
            return false;
        }

        return true;
    }

    /**
     * Make the SOAP request using the info provided by the entity
     *
     * @param HttpEntity $httpEntity
     *
     * @return bool
     */
    private static function runSoap (HttpEntity $httpEntity): bool
    {
        try
        {
            $soap = new \SoapClient(
                $httpEntity->getWsdlUrl(),
                $httpEntity->getConfig()
            );

            $method = $httpEntity->getMethod();
            $requestData = $httpEntity->getPostData();

            if (is_array($requestData))
            {
                $soap->__setSoapHeaders($httpEntity->getHeaders());

                $response = $soap->$method($requestData);
            }
            else if (is_string($requestData))
            {
                $response = $soap->__doRequest(
                    $requestData,
                    $httpEntity->getWsdlUrl(),
                    $method,
                    $httpEntity->getSoapVersion(),
                    0
                );
            }
        }
        catch (\Throwable $th)
        {
            $httpEntity->addError($th->getMessage());
            return false;
        }

        $httpEntity->setRawRequest($soap->__getLastRequest());
        $httpEntity->parseResponse(Type::getProperty($response, "{$method}Result") ?? $response);

        if ($httpEntity->hasErrors())
        {
            return false;
        }

        return true;
    }
}
