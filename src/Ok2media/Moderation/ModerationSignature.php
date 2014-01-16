<?php

namespace Ok2media\Moderation;

use Guzzle\Common\Event;
use Guzzle\Common\Collection;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Http\QueryString;
use Guzzle\Http\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Signs all requests to the Moderation service with a HMAC signature
 */
class ModerationSignature implements EventSubscriberInterface
{

    /** @var Collection Configuration settings */
    protected $config = array();

    /**
     * @param	Array	$config		Configuration array containing these parameters:
     *  - string    'private_key'   The API private key from your account
     *  - string    'public_key'    The API public key from your account     
     */
    public function __construct($config)
    {
        $this->config = Collection::fromConfig($config, array(), array(
            'private_key', 'public_key'
        ));
    }

    public static function getSubscribedEvents()
    {
        return array(
            'client.create_request' => array('onClientCreateRequest', 255)
        );
    }

    /**
     * Client create-request event handler
     * 
     * @param	Event	$event	Event recieved
     */
    public function onClientCreateRequest(Event $event)
    {
        $timestamp = $this->getTimestamp($event);
        $request = $event['request'];

        $signature = $this->signature(
            $request->getMethod(),
            $request->getUrl(),
            $timestamp,
            $this->config['private_key']
        );

        $query = $event['request']->getQuery();

        $query->set('apikey', $this->config['public_key']);
        $query->set('time', $timestamp);
        $query->set('signature', $signature);

    }
    
    /**
     * Create the signature from the request parameters
     * 
     * @param	String	$verb		HTTP verb (POST, GET, PUT, DELETE etc)
     * @param	String	$url		Request URL
     * @param	Integer	$time		Timestamp for the request
     * @param	String	$private	API private key
     * 
     * @return	String			Request signature
     */
    public function signature($verb, $url, $time, $private) {
        $string_data =  strtoupper($verb) . "\n" .
                        strtolower($url) . "\n" .
                        $time;
        return hash_hmac('sha256', $string_data, $private);
    }

    /**
     * Gets timestamp from event or create new timestamp
     *
     * @param Event $event Event containing contextual information
     *
     * @return int
     */
    public function getTimestamp(Event $event)
    {
       return $event['timestamp'] ?: time();
    }
}
