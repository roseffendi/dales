<?php

namespace Roseffendi\Dales;

interface DTDataProvider
{
    /**
     * Perform ordering
     * @param  array  $orders
     * @param  array  $columns
     * @return self
     */
    public function dtOrder(array $orders, array $columns);

    /**
     * Perform pagination, -1 length indicate no limit
     * @param  integer $start
     * @param  integer $length
     * @return self
     */
    public function dtPaginate($start, $length);

    /**
     * Count current result
     * @return integer
     */
    public function dtCount();

    /**
     * Get current result
     * @return array
     */
    public function dtGet();

    /**
     * Retrieve queryable columns
     * @return array
     */
    public function dtGetAvailableColumns();

    /**
     * Perform filter for each columns
     * @param  array $columns
     * @return self
     */
    public function dtPerformColumnFilter($columns);

    /**
     * Perform filter for global
     * @param  array  $columns
     * @param  string $dtParamProvider
     * @return self
     */
    public function dtPerformGlobalFilter($columns, $search = '');
}