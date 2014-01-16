<?php

namespace Ok2media\Moderation\Response;

use Guzzle\Service\Command\ResponseClassInterface;
use Guzzle\Service\Command\OperationCommand;

/**
 * Generic response handler
 */
class Response implements ResponseClassInterface
{
    protected $name;

    public static function fromCommand(OperationCommand $command)
    {
        $response = $command->getResponse()->json();
        // If 'ok' is not set or not true, something went wrong
        if (!isset($response['response']) || empty($response['ok']) || $response['ok'] != 1) {
            throw new \Exception('response not ok');
        }
        // If response is not an array return true if 1, otherwise false
        if (!is_array($response['response'])) {
            return ($response['response'] == 1) ? true : false;
        }
        return ($response['response']);
    }

    public function __construct($name)
    {
        $this->name = $name;
    }

}