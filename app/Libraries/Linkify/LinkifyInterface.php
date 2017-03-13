<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\Linkify;

/**
 * Interface LinkifyInterface
 * @package Kabooodle\Libraries\Linkify
 */
interface LinkifyInterface
{
    /**
     * Add HTML links to both URLs and email addresses.
     *
     * @param string $text    Text to process.
     * @param array  $options Options.
     *
     * @return string Processed text.
     */
    public function process($text, array $options = []);

    /**
     * Add HTML links to URLs.
     *
     * @param string $text    Text to process.
     * @param array  $options Options.
     *
     * @return string Processed text.
     */
    public function processUrls($text, array $options = []);

    /**
     * Add HTML links to email addresses.
     *
     * @param string $text    Text to process.
     * @param array  $options Options.
     *
     * @return string Processed text.
     */
    public function processEmails($text, array $options = []);
}
