<?php

namespace App\Sharp\Customer;

use App\Eloquent\Customer;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

class ListCustomer extends SharpEntityList
{
    /**
    * Build list containers using ->addDataContainer()
    *
    * @return void
    */
    public function buildListDataContainers()
    {
        $this->addDataContainer(
            EntityListDataContainer::make('first_name')
                ->setLabel('Name')
                ->setSortable()
                ->setHtml()
        )->addDataContainer(
            EntityListDataContainer::make('last_name')
                ->setLabel('Last Name')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('state')
                ->setLabel('State')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('answer_state')
                ->setLabel('Answer State')
                ->setSortable()
        )->addDataContainer(
            EntityListDataContainer::make('created_at')
                ->setLabel('Created at')
                ->setSortable()
        );
    }

    /**
    * Build list layout using ->addColumn()
    *
    * @return void
    */

    public function buildListLayout()
    {
        $this->addColumn('first_name', 3)
            ->addColumn('last_name', 3)
            ->addColumn('state', 2)
            ->addColumn('answer_state', 2)
            ->addColumn('created_at', 2);
    }

    /**
    * Build list config
    *
    * @return void
    */
    public function buildListConfig()
    {
        $this->setInstanceIdAttribute('id')
//            ->setSearchable()
//            ->setDefaultSort('name', 'asc')
            ->setPaginated();
    }

    /**
    * Retrieve all rows data as array.
    *
    * @param EntityListQueryParams $params
    * @return array
    */
    public function getListData(EntityListQueryParams $params)
    {
        return $this->transform(Customer::all());
    }
}
