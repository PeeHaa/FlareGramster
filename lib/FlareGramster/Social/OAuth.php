<?php

namespace FlareGramster\Social;

use FlareGramster\Stoarge\ImmutableKeyValue;
use OAuth\Common\Http\Uri\UriFactory;
use OAuth\ServiceFactory;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;

class OAuth
{
    private $credentials;

    private $currentUri;

    private $serviceFactory;

    private $storage;

    private $services = [];

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    public function setUp(array $server)
    {
        $this->setCurrentUri($server);

        $this->serviceFactory = new ServiceFactory();

        $this->storage = new Session();

        $this->initializeServices();
    }

    private function setCurrentUri(array $server)
    {
        $uriFactory = new UriFactory();

        $this->currentUri = $uriFactory->createFromSuperGlobalArray($server);

        $this->currentUri->setQuery('');
    }

    private function initializeServices()
    {
        $returnUri = $this->currentUri->getAbsoluteUri();

        foreach ($this->credentials as $name => $serviceCredentials) {
            $uriSuffix = '/share/' . $name;
            if (substr($returnUri, -strlen($uriSuffix)) !== $uriSuffix) {
                $returnUri .= $uriSuffix;
            }

            $this->services[$name] = $this->serviceFactory->createService($name, new Credentials(
                $serviceCredentials['key'],
                $serviceCredentials['secret'],
                $returnUri
            ), $this->storage, ['publish_actions']);
        }
    }

    public function getUri($name)
    {
        return $this->services[$name]->getAuthorizationUri();
    }

    public function getAccessToken($name, $token)
    {
        return $this->services[$name]->requestAccessToken($token);
    }

    public function postMessage($name, $url, $message)
    {
        $this->services[$name]->request('/me/feed', 'POST', ['link' => $url, 'message' => $message]);
    }
}
