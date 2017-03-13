<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters\Paginators;

use Illuminate\Support\HtmlString;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Pagination\UrlWindowPresenterTrait;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Contracts\Pagination\Presenter as PresenterContract;
use Illuminate\Pagination\BootstrapFourNextPreviousButtonRendererTrait;

/**
 * Class DefaultPaginator
 * @package Kabooodle\Presenters\Paginators
 */
class DefaultPaginator implements PresenterContract
{
    use BootstrapFourNextPreviousButtonRendererTrait, UrlWindowPresenterTrait;

    /**
     * @var \Illuminate\Contracts\Pagination\Paginator
     */
    protected $paginator;

    /**
     * @var array
     */
    protected $window;

    /**
     * @param  \Illuminate\Contracts\Pagination\Paginator  $paginator
     * @param  \Illuminate\Pagination\UrlWindow|null  $window
     * @return void
     */
    public function __construct(PaginatorContract $paginator, UrlWindow $window = null)
    {
        $this->paginator = $paginator;
        $this->window = is_null($window) ? UrlWindow::make($paginator) : $window->get();
    }

    /**
     * @return bool
     */
    public function hasPages()
    {
        return $this->paginator->hasPages();
    }

    /**
     * @return \Illuminate\Support\HtmlString
     */
    public function render()
    {
        // Disabled this check to always show pagination.

        if ($this->hasPages()) {
            return new HtmlString(sprintf(
                '<ul class="pagination">%s %s %s</ul>',
                $this->getPreviousButton(),
                $this->getLinks(),
                $this->getNextButton()
            ));
        }

        return new HtmlString('');
    }

    /**
     * @param  string  $url
     * @param  int  $page
     * @param  string|null  $rel
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page, $rel = null)
    {
        $rel = is_null($rel) ? '' : ' rel="'.$rel.'"';

        return '<li class="page-item"><a class="page-link" href="'.htmlentities($url).'"'.$rel.'>'.$page.'</a></li>';
    }

    /**
     * @param  string  $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<li class="page-item disabled"><a class="page-link">'.$text.'</a></li>';
    }

    /**
     * @param  string  $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<li class="page-item active"><a class="page-link">'.$text.'</a></li>';
    }

    /**
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * @return int
     */
    protected function currentPage()
    {
        return $this->paginator->currentPage();
    }

    /**
     * @return int
     */
    protected function lastPage()
    {
        return $this->paginator->lastPage();
    }
}
