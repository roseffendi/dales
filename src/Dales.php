<?php

namespace Roseffendi\Dales;

use BadMethodCallException;
use Roseffendi\Dales\Exceptions\ColumnNotAvailableException;

class Dales
{
    /**
     * @var DTParamProvider
     */
    protected $dtParamProvider;

    /**
     * @var DTDataProvider
     */
    protected $dtDataProvider;

    /**
     * @var array
     */
    protected $customColumns = [];

    /**
     * @var array
     */
    protected $customValues = [];

    /**
     * @param DTParamProvider $dtParamProvider
     */
    public function __construct(DTParamProvider $dtParamProvider)
    {
        $this->dtParamProvider = $dtParamProvider;
    }

    /**
     * Retrieve datatables raw data
     * 
     * @return array
     */
    public function getRaw()
    {
        $dtDataProvider = $this->dtDataProvider;

        $draw = $this->dtParamProvider->getLastDraw() + 1;
        $requested = $this->dtParamProvider->getColumns();
        $available = $dtDataProvider->dtGetAvailableColumns();

        // queryable columns
        // this columns is used perform query
        $queryableColumns = $this->mapQueryableColumn($available, $requested);

        $search = $this->dtParamProvider->getSearch();
        $start = $this->dtParamProvider->getStart();
        $length = $this->dtParamProvider->getLength();
        $order = $this->dtParamProvider->getOrder();

        // get total records
        $recordsTotal = $dtDataProvider->dtCount();

        // perform filter
        $dtDataProvider = $dtDataProvider->dtPerformColumnFilter($queryableColumns)
                                         ->dtPerformGlobalFilter($queryableColumns, $search);
        // get filtered record
        $recordsFiltered = $dtDataProvider->dtCount();

        // get data after paginate and order
        $data = $dtDataProvider->dtPaginate($start, $length)
                               ->dtOrder($order, $queryableColumns)
                               ->dtGet($queryableColumns);

        // Apply custom columns and custom values to data
        $data = $this->serveData($available, $requested, $data);

        return compact('draw', 'data', 'recordsTotal', 'recordsFiltered');
    }

    /**
     * Render datatables data
     * 
     * @return array
     */
    public function render()
    {
        $raw = $this->getRaw();

        return [
            'draw' => $raw['draw'],
            'data' => $this->pivotData($raw['data']),
            'recordsTotal' => $raw['recordsTotal'],
            'recordsFiltered' => $raw['recordsFiltered'],
        ];
    }

    /**
     * Add additional column to collection
     * 
     * @param string            $name
     * @param string|callable   $value
     * @return self
     */
    public function addColumn($name, $value)
    {
        if(!in_array($name, $this->customColumns))
            $this->customColumns[] = $name;

        $this->customValues[$name] = $value;

        return $this;
    }

    /**
     * Set value to given column name
     * 
     * @param string            $name
     * @param string|callable   $value
     * @return self
     */
    public function setValue($name, $value)
    {
        $this->customValues[$name] = $value;

        return $this;
    }

    /**
     * Set DTParamProvider
     * 
     * @param DTParamProvider $dtParamProvider
     * @return self
     */
    public function setDTParamProvider(DTParamProvider $dtParamProvider)
    {
        $this->dtParamProvider = $dtParamProvider;
        return $this;
    }

    /**
     * Set DTDataProvider
     * 
     * @param DTDataProvider $dtDataProvider
     * @param array          $scopes
     * @return self
     */
    public function setDTDataProvider(DTDataProvider $dtDataProvider, $scopes = [])
    {
        $this->dtDataProvider = $dtDataProvider;

        foreach ($scopes as $scope) {
            $method = "Of" . ucfirst($scope);
            $this->dtDataProvider = $this->getScope($method);
        }

        return $this;
    }

    /**
     * Serve applicable data
     * 
     * @param  array  $available
     * @param  array  $requested
     * @param  array  $data
     * @return array
     */
    protected function serveData(array $available, array $requested, array $data)
    {
        $columns = $this->serveColumns($available, $requested);
        $customValues = $this->customValues;
        $servedData = [];

        foreach ($data as $index => $row) {
            $temp = [];

            foreach ($columns as $column) {
                if(isset($customValues[$column])) {
                    // Custom values was defined, so use those value instead
                    if(is_callable($customValues[$column])) {
                        $temp[$column] = call_user_func($customValues[$column], $data[$index]);
                    }else {
                        $temp[$column] = $customValues[$column];
                    }
                }else {
                   $temp[$column] = isset($row[$column]) ? $row[$column]: null;
                }
            }

            $servedData[] = $temp;
        }

        return $servedData;
    }

    /**
     * Serve applicable column
     * 
     * @param  array        $availables
     * @param  array        $requested
     * @return array
     */
    protected function serveColumns(array $available, array $requested)
    {
        $customColumns = $this->customColumns;

        $available = $this->parseColumn($available);

        foreach ($customColumns as $key => $value) {
            $available[] = $value;
        }

        $applicable = [];

        foreach ($requested as $key => $column) {
            if(in_array($column['name'], $available)) {
                $applicable[] = $column['name'];
            }elseif(isset($available[$key])) {
                $applicable[] = $available[$key];
            }else {
                throw new ColumnNotAvailableException("Column with index [$key] is not available");
            }
        }

        return $applicable;
    }

    /**
     * Pivot data to applicable datatables data
     * 
     * @param  array $data
     * @return array
     */
    protected function pivotData(array $data)
    {
        $pivot = [];

        foreach ($data as $index => $row) {
            $pivot[] = array_values($row);
        }

        return $pivot;
    }

    /**
     * Retrieve queryable columns
     * 
     * @param  array  $availables
     * @param  array  $columns
     * @return array
     */
    protected function mapQueryableColumn(array $availables, array $columns)
    {
        $parsed = $this->parseColumn($availables);
        $mapped = [];

        foreach ($columns as $key => $column) {
            $temp = [];
            if(array_search($columns[$key]['name'], $parsed) !== false) {
                $index = array_search($columns[$key]['name'], $parsed);
                $name = explode(' as ', $availables[$index]);
                $temp = $columns[$key];

                $temp['name'] = $name[0];
            }elseif(isset($availables[$key])) {
                $name = explode(' as ', $availables[$key]);

                $temp = $columns[$key];
                $temp['name'] = $name[0];
            }

            if(count($temp))
                $mapped[] = $temp;
        }

        return $mapped;
    }

    /**
     * Parse columns
     * 
     * @param  array    $columns
     * @param  null|int $index
     * @return array
     */
    protected function parseColumn(array $columns, $index = null)
    {
        $parsed = [];

        foreach ($columns as $column) {
            $str = explode(' as ', $column);

            $alias = is_null($index) ? count($str) - 1 : $index;

            $parsed[] = $str[$alias];
        }

        return $parsed;
    }

    /**
     * Apply scope
     * 
     * @param  string $name
     * @param  array  $arguments
     * @return self
     */
    protected function getScope($name, array $arguments = [])
    {
        $method = "dtScope" . ucfirst($name);

        if(method_exists($this->dtDataProvider, $method)){
            $this->dtDataProvider = call_user_func_array([$this->dtDataProvider, $method], $arguments);
        }else {
            throw new BadMethodCallException(get_class($this->dtDataProvider) . " has no method [$method]");
        }

        return $this;
    }

    /**
     * Dynamic call to dt data provider
     * 
     * @param  string   $name
     * @param  mixed    $arguments
     * @return self
     */
    public function __call($name, $arguments)
    {
        return $this->getScope($name, $arguments);
    }
}