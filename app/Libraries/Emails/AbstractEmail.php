<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Libraries\Emails;

use Closure;
use InvalidArgumentException;
use Illuminate\Contracts\Mail\MailQueue;

/**
 * Class AbstractEmail
 * @package Kabooodle\Libraries\Emails
 */
abstract class AbstractEmail
{
    /**
     * @var MailQueue|mixed|null
     */
    static $mailer = null;

    /**
     * @var \Illuminate\View\Factory|mixed
     */
    static $view = null;

    /**
     * @var Closure
     */
    protected $callable;

    /**
     * @var string
     */
    protected $resourceView = null;

    /**
     * @var array
     */
    protected $parameters = null;

    /**
     * AbstractEmail constructor.
     *
     * @param string  $resourceView
     * @param array   $parameters
     * @param Closure $callable
     */
    public function __construct(string $resourceView = null, array $parameters = [], Closure $callable = null)
    {
        $this->callable = $callable;
        $this->resourceView = $resourceView;
        $this->parameters = $parameters;

        return $this;
    }

    abstract public function getEmailTemplate();

    /**
     * @param $resourceView
     *
     * @return $this
     */
    public function setView($resourceView)
    {
        $this->resourceView = $resourceView;

        return $this;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param Closure $callable
     *
     * @return $this
     */
    public function setCallable(Closure $callable)
    {
        $this->callable = $callable;

        return $this;
    }

    /**
     * @return Closure
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @return string
     */
    public function getResourceView()
    {
        return $this->resourceView;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * TODO: Consider passing queue parameter such that queuing can be made optional.
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function send()
    {
        if (! $this->getCallable()) {
            throw new InvalidArgumentException('Missing [callable] when attempting to email.');
        }

        // The template we will be embedding the email's content into.
        // This is just a dot.object path representation.
        $template = $this->getEmailTemplate();

        // Render the content that should be inserted into the template.
        // Inject the content parameters into this view
        $content = $this->getView()->make($this->getResourceView(), $this->getParameters())->render();

        // For now, we're implicitly queueing all emails.
        return $this->getMailer()->queue($template, ['emailContent' => $content] + $this->getParameters(), $this->getCallable());
    }

    /**
     * @return MailQueue|mixed|null
     */
    public function getMailer()
    {
        if (! self::$mailer) {
            self::$mailer = app()->make(MailQueue::class);
        }

        return self::$mailer;
    }

    /**
     * @return \Illuminate\View\Factory|mixed
     */
    public function getView()
    {
        if (! self::$view) {
            self::$view = app()->make('view');
        }

        return self::$view;
    }
}