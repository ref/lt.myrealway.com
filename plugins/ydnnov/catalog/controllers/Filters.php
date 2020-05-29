<?php namespace Ydnnov\Catalog\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Filters extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Ydnnov.Catalog', 'main-menu-catalog', 'side-menu-filters');
    }
}