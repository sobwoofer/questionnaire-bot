<?php

namespace App\Sharp\Service;

use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;

class ShowService extends SharpShow
{
    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    public function find($id): array
    {
        // Replace/complete this code
        $service = Service::findOrFail(1);

        return $this->transform($service);
    }

    /**
     * Build show fields using ->addField()
     *
     * @return void
     */
    public function buildShowFields()
    {
         $this->addField(
            SharpShowTextField::make("name")
                ->setLabel("Name:")
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
                  $column->withSingleField("name");
              });
         });
    }

    function buildShowConfig()
    {
        //
    }
}
