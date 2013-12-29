<?php

namespace FlareGramster\Image;

use FlareGramster\Network\Http\RequestData;
use FlareGramster\Image\Graphic;

class DomainObject implements Data
{
    private $request;

    private $image;

    private $hash;

    public function __construct(RequestData $request, Graphic $image, $hash)
    {
        $this->request = $request;
        $this->image   = $image;
        $this->hash    = $hash;
    }

    public function getUserId()
    {
        return null;
    }

    public function getIp()
    {
        return $this->request->server('REMOTE_ADDR');
    }

    public function getUri()
    {
        return $this->request->post('url');
    }

    public function getWidth()
    {
        return $this->image->getWidth();
    }

    public function getHeight()
    {
        return $this->image->getHeight();
    }

    public function getMime()
    {
        return $this->image->getMime();
    }

    public function getExif()
    {
        return json_encode($this->image->getExifData());
    }

    public function getHash()
    {
        return $this->hash;
    }
}
