<?php

namespace App\Sharp\Customer;

use App\Eloquent\Customer;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;

class ShowCustomer extends SharpShow
{
    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    public function find($id): array
    {
        $entity = Customer::with('customerAnswers')->findOrFail($id);
        // Replace/complete this code

        return $this->transform($entity);
    }

    /**
     * Build show fields using ->addField()
     *
     * @return void
     */
    public function buildShowFields()
    {
        $this->addField(
            SharpShowTextField::make('first_name')
                ->setLabel('First Name:')
        )->addField(
            SharpShowTextField::make('last_name')
                ->setLabel('Last Name:')
        )->addField(
            SharpShowTextField::make('state')
                ->setLabel('State:')
        )->addField(
            SharpShowTextField::make('answer_state')
                ->setLabel('Answer State:')
        )->addField(
            SharpShowTextField::make('phone')
                ->setLabel('Phone:')
        )->addField(
            SharpShowTextField::make('created_at')
                ->setLabel('Crated At:')
        )->addField(
            SharpShowEntityListField::make('answer', 'answer')
                ->hideFilterWithValue('customer', function($instanceId) {
                    return $instanceId;
                })
                ->showEntityState(false)
                ->showReorderButton(false)
                ->showCreateButton(false)
        );
    }

    /**
     * Build show layout using ->addTab() or ->addColumn()
     *
     * @return void
     */
    public function buildShowLayout()
    {
        $this->addSection('Section', function(ShowLayoutSection $section) {
            $section->addColumn(6, function(ShowLayoutColumn $column) {
                $column->withSingleField('first_name');
                $column->withSingleField('last_name');
                $column->withSingleField('state');
                $column->withSingleField('phone');
                $column->withSingleField('answer_state');
                $column->withSingleField('created_at');
            });
        })->addEntityListSection('answers', 'answer');
    }

    function buildShowConfig()
    {
        //
    }
}
