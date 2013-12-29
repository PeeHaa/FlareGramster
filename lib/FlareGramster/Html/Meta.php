<?php

namespace FlareGramster\Html;

class Meta
{
    private $title;

    private $description = [];

    private $keywords = [];

    private $url;

    private $image = null;

    private $twitterType = 'summary';

    private $facebookType = null;

    private $locale = 'en_US';

    public function __construct($title, array $description, array $keywords, $url)
    {
        $this->title       = $title;
        $this->description = $description;
        $this->keywords    = $keywords;
        $this->url         = $url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setTwitterType($type)
    {
        $this->twitterType = $type;
    }

    public function getTags()
    {
        $tags = [
            'name' => [
                'description'    => $this->description[0] . ' ' .$this->description[1],
                'keywords'       => implode(',', $this->keywords),
                'twitter:card'   => $this->twitterType,
                'twitter:title'  => $this->description[0],
            ],
            'property' => [
                'og:title'       => $this->title,
                'og:url'         => $this->url,
                'og:site_name'   => $this->title,
                'og:locale'      => $this->locale,
                'og:description' => $this->description[0]
            ],
        ];

        if ($this->image !== null) {
            $tags['name']['twitter:image'] = $this->image;
            $tags['property']['og:image']  = $this->image;
        }

        return $tags;
    }
}
