Installation
==
The recommended way to use the PHP-SDK it to install via composer  
```json
{
    "require": {
        "ok2media/moderation": "1.*"
    }
}
```
Usage
==
Include autoload.php generated by composer and construct the client using your service url and public / private keys  
```php
require_once('vendor/autoload.php');
use Ok2media\Moderation\ModerationClient;

$service_url = 'http://demo3.ok2media.com';
$config = [
    'public_key' => '00000000-0000-0000-0000-000000000000',
    'private_key' => '00000000-0000-0000-0000-000000000000'
];

$client = new ModerationClient($service_url, $config);
```
You can then use the client to interact with the moderation service  
```php
$status = $client->getStatus(['cid'=>'comment1']);
```
Methods
==
Details of these methods can be found in the service descriptor file src\Ok2media\Moderation\client.json
* testService
* xcheck
 * **xid** - string
* getServiceUsers
* submitServiceUser
 * **username** - string
 * name - string
 * email - string
 * scopes - array
* updateServiceUser
 * **id** - string
 * **username** - string
 * name - string
 * email - string
 * scopes - array
* getMedia
 * **cid** - string
* submitMedia
 * **cid** - string
 * **mid** - string
 * **file** - string
* getStatus
 * **cid** - string
* setStatus
 * **cid** - string
 * **status** - string
* getMultipleStatus
 * **cid** - string CSV
* getUpdatedSince
 * **since** - integer
* deleteContent
 * **cid** - string
* submitContent
 * **cid** - string
 * **text** - string
 * **from** - string
 * to - array
 * tag - string
 * thread - string
 * pre - integer
 * display_strings - array
 * scopes - array
* updateContent
 * **cid** - string
 * **text** - string
 * **from** - string
 * to - array
 * tag - string
 * thread - string
 * pre - integer
 * display_strings - array
 * scopes - array
* getUser
 * **uid** - string
* submitUser
 * **uid** - string
 * **name** - string
 * **dob** - string
 * **gender** - string
* updateUser
 * **uid** - string
 * name - string
 * dob - string
 * gender - string

Callback  
==
This is an example of how to use the callback functionality  

The callback method returns an associative array containing 'cid' and 'published' 
```php
$client->callback()
```
Once you update the status you must call either $client->callback_success() or $client->callback_failure() to respond to the service that you were able to update the status otherwise it will assume something went wrong.

```php
// Handle the callback from the service, $status will be an associative array containing 'cid' and 'published'
$status = $client->callback();

$cid = $status['cid']; // The id for the content that was moderated
$published = $status['published']; // The new status of that content

// Your code to handle updating the published status of each piece of content
if ( $published == 1 ) {
    $result = publish_content($cid); // Your code that returns true or false
} else {
    $result = unpublish_content($cid); // Your code that returns true or false
}

if ( $result == true ) {
    $this->callback_success();
} else {
    $this->callback_failure();
}
```