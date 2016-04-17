<?php

namespace Roseffendi\Dales\Laravel;

use Roseffendi\Dales\DTParamProvider;
use Illuminate\Http\Request;

class ParamProvider implements DTParamProvider
{
    /**
     * @var Illuminate\Http\Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Retrieve requested columns
     * @return array
     */
    public function getColumns()
    {
        return $this->request->input('columns');
    }

    /**
     * Retrieve last draw
     * @return integer
     */
    public function getLastDraw()
    {
        return $this->request->input('draw');
    }

    /**
     * Retrieve starting point
     * @return integer
     */
    public function getStart()
    {
        return $this->request->input('start');
    }

    /**
     * Retrieve requested data length
     * @return integer
     */
    public function getLength()
    {
        return $this->request->input('length');
    }

    /**
     * Retrieve requested orders
     * @return array
     */
    public function getOrder()
    {
        return $this->request->input('order');
    }

    /**
     * Retrieve requested search
     * @return array
     */
    public function getSearch()
    {
        return $this->request->input('search');
    }

}