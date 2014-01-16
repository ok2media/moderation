<?php

namespace Ok2media\Moderation;

use Ok2media\Moderation\ModerationSignature;
use Guzzle\Common\Collection;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

/**
 * Moderation service client
 */
class ModerationClient extends Client {

    /**
     * @param	String	$service_url	Service URL
     * @param	Array	$config		Configuration array containing these parameters:
     *  - string    'private_key'   The API private key from your account
     *  - string    'public_key'    The API public key from your account     
     */
    public function __construct($service_url = '', $config = array()) {

        $config = Collection::fromConfig(
            $config,
            array(
                'api_path' => '/api/v3'
            ),
            array(
                'private_key',
                'public_key'
            )
        );

        parent::__construct($service_url . $config['api_path'], $config);

        $description = ServiceDescription::factory(__DIR__ . '/client.json');
        $this->setDescription($description);

        $ModerationSignature = new ModerationSignature(
            [
                'private_key'   => $config['private_key'],
                'public_key'    => $config['public_key']
            ]
        );

        $this->addSubscriber($ModerationSignature);

    }

}