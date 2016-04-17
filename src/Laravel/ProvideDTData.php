<?php

namespace Roseffendi\Dales\Laravel;

use DB;

trait ProvideDTData
{
    /**
     * Perform ordering
     * @param  array  $orders
     * @param  array  $columns
     * @return self
     */
    public function dtOrder(array $orders, array $columns)
    {
        foreach ($orders as $order) {
            if((isset($columns[$order['column']])) && (filter_var($columns[$order['column']]['orderable'], FILTER_VALIDATE_BOOLEAN)) && (!in_array($columns[$order['column']]['name'], $this->dtUnsortable))) {
                $this->dtModel = $this->dtModel->orderByRaw(
                    $columns[$order['column']]['name'] . ' ' . ($order['dir'] == 'asc' ? 'asc' : 'desc')
                );
            }
        }

        return $this;
    }

    /**
     * Perform pagination, -1 length indicate no limit
     * @param  integer $start
     * @param  integer $length
     * @return self
     */
    public function dtPaginate($start, $length)
    {
        if($length == -1)
            $this->dtModel = $this->dtModel->skip($start);
        else
            $this->dtModel = $this->dtModel->skip($start)->take($length);

        return $this;
    }

    /**
     * Count current result
     * @return integer
     */
    public function dtCount()
    {
        return $this->dtModel->count();
    }

    /**
     * Get current result
     * @return array
     */
    public function dtGet()
    {
        $columns = implode(',', $this->dtGetAvailableColumns());

        return $this->dtModel->select(DB::raw($columns))->get()->toArray();
    }

    /**
     * Retrieve queryable columns
     * @return array
     */
    public function dtGetAvailableColumns()
    {
        return $this->dtColumns;
    }

    /**
     * Perform filter for each columns
     * @param  array $columns
     * @return self
     */
    public function dtPerformColumnFilter($columns)
    {
        foreach ($columns as $column) {
            if((strlen($column['search']['value'] > 0)) && (!in_array($column['name'], $this->dtUnsearchable))){
                $this->dtModel = $this->dtModel->where(DB::raw($column['name']), '%'. $column['search']['value'] .'%');
            }
        }

        return $this;
    }

    /**
     * Perform filter for global
     * @param  array  $columns
     * @param  string $search
     * @return self
     */
    public function dtPerformGlobalFilter($columns, $search = '')
    {
        $this->dtModel = $this->dtModel->where(function($query) use ($columns, $search){
            foreach ($columns as $column) {
                if((filter_var($column['searchable'], FILTER_VALIDATE_BOOLEAN)) && (!in_array($column['name'], $this->dtUnsearchable))) {
                    $query->orWhere(DB::raw($column['name']),'LIKE','%'.$search['value'].'%');
                }
            }
        });

        return $this;
    }
}