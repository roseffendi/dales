<?php

namespace Roseffendi\Dales;

interface DTParamProvider
{
    /**
     * Retrieve requested columns
     * @return array
     */
    public function getColumns();

    /**
     * Retrieve last draw
     * @return integer
     */
    public function getLastDraw();

    /**
     * Retrieve starting point
     * @return integer
     */
    public function getStart();

    /**
     * Retrieve requested data length
     * @return integer
     */
    public function getLength();

    /**
     * Retrieve requested orders
     * @return array
     */
    public function getOrder();

    /**
     * Retrieve requested search
     * @return array
     */
    public function getSearch();
}