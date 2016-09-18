<?php

namespace Kabooodle\Libraries\Emails;

use Closure;
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
    protected $resourceView;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * AbstractEmail constructor.
     *
     * @param string  $resourceView
     * @param array   $parameters
     * @param Closure $callable
     */
    public function __construct($resourceView, array $parameters = [], Closure $callable)
    {
        $this->callable = $callable;
        $this->resourceView = $resourceView;
        $this->parameters = $parameters;

        return $this;
    }

    abstract public function getEmailTemplate();

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
     * @return bool
     */
    public function send()
    {
        // The template we will be embedding the email's content into.
        // This is just a dot.object path representation.
        $template = $this->getEmailTemplate();

        // Render the content that should be inserted into the template.
        // Inject the content parameters into this view
        $content = $this->getView()->make($this->getResourceView(), $this->getParameters())->render();

        // For now, we're implicitly queueing all emails.
        $this->getMailer()->queue($template, ['emailContent' => $content] + $this->getParameters(), $this->getCallable());
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