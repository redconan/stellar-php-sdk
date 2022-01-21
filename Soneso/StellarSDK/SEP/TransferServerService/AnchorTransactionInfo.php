<?php

namespace Soneso\StellarSDK\SEP\TransferServerService;

use Soneso\StellarSDK\Responses\Response;

class AnchorTransactionInfo extends Response {

    private bool $enabled;
    private ?bool $authenticationRequired = null;

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return bool|null
     */
    public function getAuthenticationRequired(): ?bool
    {
        return $this->authenticationRequired;
    }


    protected function loadFromJson(array $json) : void {
        if (isset($json['enabled'])) $this->enabled = $json['enabled'];
        if (isset($json['authentication_required'])) $this->authenticationRequired = $json['authentication_required'];
    }

    public static function fromJson(array $json) : AnchorTransactionInfo
    {
        $result = new AnchorTransactionInfo();
        $result->loadFromJson($json);
        return $result;
    }
}