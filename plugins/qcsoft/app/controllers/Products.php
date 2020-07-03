<?php namespace Qcsoft\App\Controllers;

use Backend\Classes\Controller;
use Backend\Widgets\Form;
use Backend\Widgets\Lists;
use BackendMenu;
use Qcsoft\App\Models\Product;
use Qcsoft\Ocext\Behaviors\ColumnInputController;
use Qcsoft\App\Models\Customergroup;
use Qcsoft\App\Models\Filter;
use Qcsoft\Ocext\Behaviors\MakeSlugController;
use Qcsoft\App\Models\Filteroption;
use Qcsoft\App\Models\FilteroptionProduct;

class Products extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        MakeSlugController::class,
        ColumnInputController::class,
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Qcsoft.App', 'main-menu-app', 'side-menu-products');
    }

    public function listExtendColumns(Lists $lists)
    {
        $lists->addColumns(Filter::get()
            ->mapWithKeys(function ($filter) {
                return [
                    "filter_$filter->id" => [
                        'label' => $filter->name,
                        'type'  => 'text',
                    ],
                ];
            })
            ->toArray()
        );
    }

    public function listExtendQuery($query, $definition)
    {
        $productTable = (new Product())->getTable();
        $filteroptionTable = (new Filteroption())->getTable();
        $filteroptionProductTable = (new FilteroptionProduct())->getTable();

        foreach (Filter::get() as $filter)
        {
            $query->addSelect(\DB::raw(<<<EOT
        (
           select group_concat(fo.name separator ', ')
           from $filteroptionProductTable fop
                    join $filteroptionTable fo on fop.filteroption_id = fo.id
           where fop.product_id = $productTable.id
           and fo.filter_id = $filter->id
        ) as filter_$filter->id
EOT
            ));
        }

        $query->with('main_image');
    }

    public function formExtendFieldsBefore(Form $form)
    {
        $filters = Filter::with('filteroptions')->get();

        foreach ($filters as $filter)
        {
            $form->tabs['fields']['filter_options[' . $filter->id . ']'] = [
                'label'       => $filter->name . ' (' . $filter->id . ')',
                'span'        => 'auto',
                'type'        => 'checkboxlist',
                'quickselect' => true,
                'options'     => $filter->filteroptions
                    ->sortBy('sort_order')
                    ->map(function ($item) {
                        $item->name = $item->name . ' (' . $item->id . ')';
                        return $item;
                    })
                    ->lists('name', 'id'),
                'tab'         => 'Filters'
            ];
        }

        $customergroups = Customergroup::all();

        foreach ($customergroups as $customergroup)
        {
            $form->tabs['fields']['customergroup_price[' . $customergroup->id . ']'] = [
                'label'               => 'Price for customer group "' . $customergroup->name . '" (' . $customergroup->id . ')',
                'attributes'          => [
                    'style' => 'position: absolute; left: 350px; top: 0; width: 150px'
                ],
                'containerAttributes' => [
                    'style' => 'clear: both; line-height: 32px'
                ],
                'type'                => 'number',
                'tab'                 => 'Price'
            ];
        }
    }
}