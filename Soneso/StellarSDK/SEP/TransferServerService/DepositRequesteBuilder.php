<?php declare(strict_types=1);

// Copyright 2022 The Stellar PHP SDK Authors. All rights reserved.
// Use of this source code is governed by a license that can be
// found in the LICENSE file.

namespace Soneso\StellarSDK\SEP\TransferServerService;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Soneso\StellarSDK\Requests\RequestBuilder;
use Soneso\StellarSDK\Requests\RequestType;
use Soneso\StellarSDK\Responses\ResponseHandler;

class DepositRequesteBuilder extends RequestBuilder
{
    private string $jwtToken;

    /**
     * @param Client $httpClient
     * @param string $jwtToken
     */
    public function __construct(Client $httpClient, string $jwtToken)
    {
        $this->jwtToken = $jwtToken;
        parent::__construct($httpClient, "deposit");
    }

    public function forQueryParameters(array $queryParameters) : DepositRequesteBuilder {
        $this->queryParameters = array_merge($this->queryParameters, $queryParameters);
        return $this;
    }

    /**
     * @param string $url
     * @return InfoResponse
     * @throws GuzzleException
     */
    public function request(string $url) : DepositResponse {
        $headers = array();
        $headers = array_merge($headers, RequestBuilder::HEADERS);
        $headers = array_merge($headers, ['Authorization' => "Bearer ".$this->jwtToken]);
        $request = new Request("GET", $url, $headers);
        $response = $this->httpClient->send($request);
        $responseHandler = new ResponseHandler();
        return $responseHandler->handleResponse($response, RequestType::ANCHOR_INFO, $this->httpClient);
    }

    /**
     * Build and execute request.
     * @return DepositResponse
     * @throws GuzzleException
     */
    public function execute() : DepositResponse {
        return $this->request($this->buildUrl());
    }
}