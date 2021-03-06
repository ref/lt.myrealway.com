<?php namespace Qcsoft\App\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Qcsoft\Ocext\Behaviors\ColumnInputController;

class Customergroups extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        ColumnInputController::class,
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Qcsoft.App', 'main-menu-customers', 'side-menu-customergroups');
    }
}
