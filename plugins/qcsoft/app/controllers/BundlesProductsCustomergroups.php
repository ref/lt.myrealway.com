<?php namespace Qcsoft\App\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class BundlesProductsCustomergroups extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Qcsoft.App', 'main-menu-app', 'side-menu-bundles-products-customergroups');
    }
}
