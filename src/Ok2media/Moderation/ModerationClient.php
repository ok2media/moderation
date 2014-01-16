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
    
    /**
     * Handle a callback request from the moderation service
     * 
     * @return	Array		Result Array which contains:
     *  - string    'cid'       The content ID
     *  - integer   'published' The new status of the content
     */
    public function callback() {
        if (!isset($_POST['content'])) {
            throw new \Exception('Variable Error', 500);
        }
        
        if (get_magic_quotes_gpc()) {
            $_POST['content'] = stripslashes($_POST['content']);
        }
    
        $content = json_decode($_POST['content']);
    
        if (!is_object($content) || !isset($content->action) || !isset($content->cid) || !isset($content->xid)) {
            throw new \Exception('Variable Error', 500);
        }
        
        if (!$this->xcheck(['xid'=>$content->xid])) {
            throw new \Exception('Not Found', 404);
        }

        switch ($content->action) {
            case 'test':
                exit(json_encode(array('success'=>true)));
            case 'publish':
                return [
                    'cid'   => $content->cid,
                    'published' => 1
                ];
            case 'unpublish':
                return [
                    'cid'   => $content->cid,
                    'published' => 1
                ];
            default:
                throw new \Exception('There has been an error', 500);
        }
    }
    
    /**
     * Returns a result to the moderation service that the callback was successful
     * 
     * @return	null	Exit with json array success false
     */
    public function callback_success() {
        exit(json_encode(array('success'=>true)));
    }
    
    /**
     * Returns a result to the moderation service that the callback was unsuccessful
     * 
     * @return	null	Exit with json array success false
     */
    public function callback_failure() {
        exit(json_encode(array('success'=>false)));
    }

}