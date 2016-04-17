<?php

namespace tests\Roseffendi\Dales;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Roseffendi\Dales\DTParamProvider;
use Roseffendi\Dales\DTDataProvider;

class DalesSpec extends ObjectBehavior
{
    function let(DTParamProvider $dtParamProvider)
    {
        $this->beConstructedWith($dtParamProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Roseffendi\Dales\Dales');
    }

    function it_provide_raw_data(DTParamProvider $dtParamProvider, DTDataProvider $dtDataProvider)
    {
        $params = $this->get_params();
        $results = $this->get_data_result();
        $expected = $this->get_expected();

        $dtParamProvider->getLastDraw()->shouldBeCalled()->willReturn(1);
        $dtParamProvider->getColumns()->shouldBeCalled()->willReturn($params['columns']);
        $dtDataProvider->dtGetAvailableColumns()->shouldBeCalled()->willReturn(['col.1', 'col.2', 'col.3']);

        $dtParamProvider->getSearch()->shouldBeCalled()->willReturn($params['search']);
        $dtParamProvider->getStart()->shouldBeCalled()->willReturn(1);
        $dtParamProvider->getLength()->shouldBeCalled()->willReturn(10);
        $dtParamProvider->getOrder()->shouldBeCalled()->willReturn($params['order']);

        $dtDataProvider->dtCount()->shouldBeCalled()->willReturn(1000);

        $dtDataProvider->dtPerformColumnFilter($params['columns'])
                       ->shouldBeCalled()
                       ->willReturn($dtDataProvider);

        $dtDataProvider->dtPerformGlobalFilter($params['columns'], $params['search'])
                       ->shouldBeCalled()
                       ->willReturn($dtDataProvider);

        $dtDataProvider->dtPaginate(1, 10)->shouldBeCalled()->willReturn($dtDataProvider);
        $dtDataProvider->dtOrder($params['order'], $params['columns'])->shouldBeCalled()->willReturn($dtDataProvider);

        $dtDataProvider->dtGet($params['columns'])->shouldBeCalled()->willReturn($results);

        $this->setDTDataProvider($dtDataProvider)
             ->getRaw()
             ->shouldEqual($expected);
    }

    function it_provide_rendered_data(DTParamProvider $dtParamProvider, DTDataProvider $dtDataProvider)
    {
        $params = $this->get_params();
        $results = $this->get_data_result();
        $expected = $this->get_expected('rendered');

        $dtParamProvider->getLastDraw()->shouldBeCalled()->willReturn(1);
        $dtParamProvider->getColumns()->shouldBeCalled()->willReturn($params['columns']);
        $dtDataProvider->dtGetAvailableColumns()->shouldBeCalled()->willReturn(['col.1', 'col.2', 'col.3']);

        $dtParamProvider->getSearch()->shouldBeCalled()->willReturn($params['search']);
        $dtParamProvider->getStart()->shouldBeCalled()->willReturn(1);
        $dtParamProvider->getLength()->shouldBeCalled()->willReturn(10);
        $dtParamProvider->getOrder()->shouldBeCalled()->willReturn($params['order']);

        $dtDataProvider->dtCount()->shouldBeCalled()->willReturn(1000);

        $dtDataProvider->dtPerformColumnFilter($params['columns'])
                       ->shouldBeCalled()
                       ->willReturn($dtDataProvider);

        $dtDataProvider->dtPerformGlobalFilter($params['columns'], $params['search'])
                       ->shouldBeCalled()
                       ->willReturn($dtDataProvider);

        $dtDataProvider->dtPaginate(1, 10)->shouldBeCalled()->willReturn($dtDataProvider);
        $dtDataProvider->dtOrder($params['order'], $params['columns'])->shouldBeCalled()->willReturn($dtDataProvider);

        $dtDataProvider->dtGet($params['columns'])->shouldBeCalled()->willReturn($results);

        $this->setDTDataProvider($dtDataProvider)
             ->render()
             ->shouldEqual($expected);
    }

    function it_can_add_column(DTParamProvider $dtParamProvider, DTDataProvider $dtDataProvider)
    {
        $params = $this->get_params();
        $results = $this->get_data_result();
        $expected = $this->get_expected();

        $originalColumsn = $params['columns'];

        $params['columns'][] = [
            'data' => '',
            'name' => 'col.4',
            'searchable' => true,
            'orderable' => true,
            'search' => [
                'value' => '',
                'regex' => ''
            ]
        ];

        $dtParamProvider->getLastDraw()->shouldBeCalled()->willReturn(1);
        $dtParamProvider->getColumns()->shouldBeCalled()->willReturn($params['columns']);
        $dtDataProvider->dtGetAvailableColumns()->shouldBeCalled()->willReturn(['col.1', 'col.2', 'col.3']);

        $dtParamProvider->getSearch()->shouldBeCalled()->willReturn($params['search']);
        $dtParamProvider->getStart()->shouldBeCalled()->willReturn(1);
        $dtParamProvider->getLength()->shouldBeCalled()->willReturn(10);
        $dtParamProvider->getOrder()->shouldBeCalled()->willReturn($params['order']);

        $dtDataProvider->dtCount()->shouldBeCalled()->willReturn(1000);

        $dtDataProvider->dtPerformColumnFilter($originalColumsn)
                       ->shouldBeCalled()
                       ->willReturn($dtDataProvider);

        $dtDataProvider->dtPerformGlobalFilter($originalColumsn, $params['search'])
                       ->shouldBeCalled()
                       ->willReturn($dtDataProvider);

        $dtDataProvider->dtPaginate(1, 10)->shouldBeCalled()->willReturn($dtDataProvider);
        $dtDataProvider->dtOrder($params['order'], $originalColumsn)->shouldBeCalled()->willReturn($dtDataProvider);

        $dtDataProvider->dtGet($originalColumsn)->shouldBeCalled()->willReturn($results);

        $this->setDTDataProvider($dtDataProvider)
             ->addColumn('col.4', function($data){
                return 'col4';
             })
             ->getRaw()
             ->shouldEqual($expected);   
    }

    function it_can_modify_column_value(DTParamProvider $dtParamProvider, DTDataProvider $dtDataProvider)
    {
        $params = $this->get_params();
        $results = $this->get_data_result();
        $expected = $this->get_expected();

        foreach ($expected['data'] as $key => $value) {
            $expected['data'][$key]['col.1'] .= '-modified';
        }

        $dtParamProvider->getLastDraw()->shouldBeCalled()->willReturn(1);
        $dtParamProvider->getColumns()->shouldBeCalled()->willReturn($params['columns']);
        $dtDataProvider->dtGetAvailableColumns()->shouldBeCalled()->willReturn(['col.1', 'col.2', 'col.3']);

        $dtParamProvider->getSearch()->shouldBeCalled()->willReturn($params['search']);
        $dtParamProvider->getStart()->shouldBeCalled()->willReturn(1);
        $dtParamProvider->getLength()->shouldBeCalled()->willReturn(10);
        $dtParamProvider->getOrder()->shouldBeCalled()->willReturn($params['order']);

        $dtDataProvider->dtCount()->shouldBeCalled()->willReturn(1000);

        $dtDataProvider->dtPerformColumnFilter($params['columns'])
                       ->shouldBeCalled()
                       ->willReturn($dtDataProvider);

        $dtDataProvider->dtPerformGlobalFilter($params['columns'], $params['search'])
                       ->shouldBeCalled()
                       ->willReturn($dtDataProvider);

        $dtDataProvider->dtPaginate(1, 10)->shouldBeCalled()->willReturn($dtDataProvider);
        $dtDataProvider->dtOrder($params['order'], $params['columns'])->shouldBeCalled()->willReturn($dtDataProvider);

        $dtDataProvider->dtGet($params['columns'])->shouldBeCalled()->willReturn($results);

        $this->setDTDataProvider($dtDataProvider)
             ->setValue('col.1', function($data){
                return $data['col.1'].'-modified';
             })
             ->getRaw()
             ->shouldEqual($expected);
    }

    protected function get_data_result()
    {
        $results = [];

        for ($i=0; $i < 10 ; $i++) { 
            $results[] = [
                'col.1' => 'col1-'.$i,
                'col.2' => 'col2-'.$i,
                'col.3' => 'col3-'.$i
            ];
        }

        return $results;
    }

    protected function get_data_expected_raw()
    {
        $data = [];

        for ($i=0; $i < 10; $i++) { 
            $data[] = [
                'col.1' => 'col1-'.$i,
                'col.2' => 'col2-'.$i,
                'col.3' => 'col3-'.$i,
            ];
        }

        return $data;
    }

    protected function get_data_expected_rendered()
    {
        $data = [];

        for ($i=0; $i < 10; $i++) { 
            $data[] = [
                'col1-'.$i,
                'col2-'.$i,
                'col3-'.$i
            ];
        }

        return $data;
    }

    protected function get_expected($type = 'raw')
    {
        $data = $type == 'raw' ? $this->get_data_expected_raw() : $this->get_data_expected_rendered();

        return [
            'draw' => 2,
            'data' => $data,
            'recordsTotal' => 1000,
            'recordsFiltered' => 1000
        ];
    }

    protected function get_params()
    {
        $columns = [
            [
                'data' => '',
                'name' => 'col.2',
                'searchable' => true,
                'orderable' => true,
                'search' => [
                    'value' => '',
                    'regex' => ''
                ]
            ],
            [
                'data' => '',
                'name' => 'col.1',
                'searchable' => true,
                'orderable' => true,
                'search' => [
                    'value' => 'yyyyy',
                    'regex' => ''
                ]
            ],
            [
                'data' => '',
                'name' => 'col.3',
                'searchable' => true,
                'orderable' => true,
                'search' => [
                    'value' => '',
                    'regex' => ''
                ]
            ]
        ];

        $search = [
            'value' => 'asdasd',
            'regex' => ''
        ];

        $order = [
            [
                'column' => 1,
                'dir' => 'asc'
            ]
        ];

        return compact('columns', 'search', 'order');
    }
}
