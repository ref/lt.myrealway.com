<?php namespace Qcsoft\App\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Qcsoft\App\Classes\ImportOldSite;
use Qcsoft\App\GraphQL\AppContext;
use Qcsoft\App\GraphQL\AppGraphQL;
use Qcsoft\App\Models\Auser;
use Qcsoft\App\Models\Category;
use Qcsoft\App\Models\FilteroptionProduct;
use Qcsoft\App\Models\Product;
use Qcsoft\Ocext\Behaviors\MaintenanceController;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\GraphQL;


class Dev extends Controller
{
    public $implement = [MaintenanceController::class];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Qcsoft.App', 'main-menu-app', 'side-menu-dev');
    }

    public function index()
    {
        $appContext = new AppContext();

        $result = AppGraphQL::execute(<<<EOT
{
    bundle (selectWith: ["bundle_products"]) {
        bundle_products {
            id
        }
    }
}
EOT
            , [], $appContext);

        dump($result->toArray());
        die;
    }

//    public function index()
//    {
//        \Debugbar::disable();
//
//        $this->importOldSite();
//    }

    public function importOldSite()
    {
        $products = Product::all();

        foreach ($products as $product)
        {
            $product->delete();
        }

        $categories = Category::all();

        foreach ($categories as $category)
        {
            $category->delete();
        }

        (new ImportOldSite())->import();

        return $this->asExtension(MaintenanceController::class)->index();
    }

    /**
     * This method here is just for convenience, it should be removed in the future
     */
    protected function makeRandomGenderFilterBindings()
    {
        foreach (Product::all() as $product)
        {
            $filterOptions = [
                                 ['male'],
                                 ['female'],
                                 ['male', 'female'],
                             ][rand(0, 2)];

            foreach ($filterOptions as $i => $foption)
            {
                $fop = new FilteroptionProduct();
                $fop->product_id = $product->id;
                $fop->filteroption_id = $i + 1;
                $fop->save();
            }
        }
    }

//    public function index()
//    {
//        dump(Auser::first()->aphone->num);
//        dump(Auser::first()->aphone());
//        die;
//    }

}
