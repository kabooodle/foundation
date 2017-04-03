<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Claims;

use Binput;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Http\Controllers\Traits\ResponseTrait;
use Kabooodle\Repositories\Claims\ClaimsRepositoryInterface;
use Kabooodle\Transformers\Claims\ClaimsMerchantTransformer;

/**
 * Class ClaimsMerchantApiController
 */
class ClaimsMerchantApiController extends AbstractApiController
{
    use Helpers, PaginatesTrait, ResponseTrait;

    /**
     * @var ClaimsRepositoryInterface
     */
    public $claimsRepository;

    /**
     * @param ClaimsRepositoryInterface $claimsRepository
     */
    public function __construct(ClaimsRepositoryInterface $claimsRepository)
    {
        $this->claimsRepository = $claimsRepository;
    }

    /**
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request)
    {
        $claims = $this->claimsRepository->getAllClaimsOnUserListables($this->getUser()->id);

        return $this->response->paginator($this->paginateData($request, collect($claims)), new ClaimsMerchantTransformer);
    }

    /**
     * @param Request $reqest
     *
     * @return \Dingo\Api\Http\Response
     */
    public function accept(Request $reqest)
    {
        $claims = $this->claimsRepository->accept($this->getUser()->id, Binput::get('claims'));

        return $this->response->collection(collect($claims), new ClaimsMerchantTransformer);
    }

    /**
     * @param Request $reqest
     *
     * @return \Dingo\Api\Http\Response
     */
    public function reject(Request $reqest)
    {
        $claims = $this->claimsRepository->reject($this->getUser()->id, Binput::get('claims'));

        return $this->response->collection(collect($claims), new ClaimsMerchantTransformer);
    }

    /**
     * @param Request $reqest
     *
     * @return \Dingo\Api\Http\Response
     */
    public function label(Request $reqest)
    {
        $claims = $this->claimsRepository->label($this->getUser()->id, Binput::get('claims'), Binput::get('labels'));

        return $this->response->collection(collect($claims), new ClaimsMerchantTransformer);
    }
}
