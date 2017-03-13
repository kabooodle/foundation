<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\Emojify;


use DOMDocument;
use Emojione\Client;

/**
 * Class EmojifyClient
 */
class EmojifyClient extends Client
{
    /**
     * @var string
     */
    public $htmlClasses;

    /**
     * {@inheritDoc}
     */
    public function toImage($string)
    {
        $string = parent::toImage($string);

        $dom = new DOMDocument;
        $dom->loadHTML($string);

        $images = $dom->getElementsByTagName('img');
        if ($images) {
            foreach($images as $image) {
                $classes = $image->getAttribute('class');
                $image->setAttribute('class', $classes.'  '.$this->htmlClasses);
            }
            $dom = $dom->saveHTML();

            return $dom;
        }

        return $string;
    }
}
