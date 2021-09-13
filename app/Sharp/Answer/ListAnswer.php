<?php

namespace App\Sharp\Answer;

use App\Eloquent\CustomerAnswer;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

class ListAnswer extends SharpEntityList
{
    /**
    * Build list containers using ->addDataContainer()
    *
    * @return void
    */
    public function buildListDataContainers()
    {
        $this->addDataContainer(
            EntityListDataContainer::make('answer')
                ->setLabel('Answer')
                ->setSortable()
                ->setHtml()
        )->addDataContainer(
            EntityListDataContainer::make('question')
                ->setLabel('Question')
                ->setSortable()
                ->setHtml()
        )->addDataContainer(
            EntityListDataContainer::make('created_at')
                ->setLabel('Date')
                ->setSortable()
                ->setHtml()
        );
    }

    /**
    * Build list layout using ->addColumn()
    *
    * @return void
    */

    public function buildListLayout()
    {
        $this->addColumn('question', 6)
            ->addColumn('answer', 4)
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
            ->setSearchable(false)
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
        $entity = CustomerAnswer::select('customer_answer.*')->distinct();

        if ($ids = $params->specificIds()) {
            $entity->whereIn('id', $ids);
        } else {
            if ($customerId = $params->filterFor('customer')) {
                $entity->leftJoin('question', 'question.id', '=', 'customer_answer.question_id')
                    ->where('customer_answer.customer_id', $customerId);
            }
        }

        $this->setCustomTransformer('question', function($vendors, CustomerAnswer $item) {
            return $item->question->question_ru;
        });

        return $this->transform($entity->paginate(30));
    }
}
