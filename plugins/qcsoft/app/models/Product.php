<?php namespace Qcsoft\App\Models;

use Qcsoft\App\Modelsbase\ProductBase;

class Product extends ProductBase
{
    public static function getPageRequireEntities($ids)
    {
        $products = static::query()
            ->whereIn('id', $ids)
            ->with([
                'catalogitem',
                'page',
                'catalogitem.catalogitem_relevant_catalogitems',
                'catalogitem.catalogitem_relevant_catalogitems.relevant_catalogitem'            => function ($query)
                {
                    $query->select(['id', 'owner_type_id', 'owner_id']);
                },
                'catalogitem.catalogitem_relevant_catalogitems.relevant_catalogitem.owner'      => function ($query)
                {
                    $query->select(['id']);
                },
                'catalogitem.catalogitem_relevant_catalogitems.relevant_catalogitem.owner.page' => function ($query)
                {
                    $query->select(['id', 'owner_type_id', 'owner_id']);
                },
            ])
            ->get();
//        dd($products->toArray());

        $resultItems = [];

        foreach ($products as $product)
        {
            $result = [
                'bundle'      => [],
                'catalogitem' => [],
                'image'       => [],
                'page'        => [],
                'product'     => [],
            ];

            $result['catalogitem'][] = $product->catalogitem->id;
            $result['product'][] = $product->id;

            $relevantItems = $product->catalogitem->catalogitem_relevant_catalogitems;

            foreach ($relevantItems as $relevantItem)
            {
//                dump($relevantItem->toArray());
                /** @var Catalogitem $relevantCatalogitem */
                $relevantCatalogitem = $relevantItem->relevant_catalogitem;
                $result['catalogitem'][] = $relevantCatalogitem->id;
                $result['page'][] = $relevantCatalogitem->owner->page->id;
                $result[Entity::typeById($relevantCatalogitem->owner_type_id)][] = $relevantCatalogitem->owner_id;
            }

            $resultItems[$product->page->id] = $result;
        }
//        dd($resultItems);
        return $resultItems;
    }

    public function getCatalogitemPriceAttribute($value)
    {
        return $value / 100;
    }

    public function setCatalogitemPriceAttribute($value)
    {
        return $this->attributes['catalogitem_price'] = $value * 100;
    }



//    public function getH1TitleAttribute()
//    {
//        return $this->page->custom_h1_title ?: $this->catalogitem->name;
//    }
//
//    protected static function boot()
//    {
//        parent::boot();
//
//        static::extend(function ($model)
//        {
//            /** @var static $model */
//
//            ////////////////////////////////////////////////////////////////////////////////
//            /// Auto delete related
//            ////////////////////////////////////////////////////////////////////////////////
//            $model->hasMany['product_categories']['delete'] = true;
//            $model->hasMany['product_bundles']['delete'] = true;
//            $model->hasMany['product_filteroptions']['delete'] = true;
//
//            $model->morphOne['catalogitem']['delete'] = true;
//
//            ////////////////////////////////////////////////////////////////////////////////
//            /// Sellable
//            ////////////////////////////////////////////////////////////////////////////////
//            $model->morphMany['cartitems'] = [Cartitem::class, 'name' => 'sellable'];
//
//            ////////////////////////////////////////////////////////////////////////////////
//            /// Convert array of text inputs to related model records
//            ////////////////////////////////////////////////////////////////////////////////
//            $model->bindEvent('model.saveInternal', function () use ($model, &$customergroupPrices)
//            {
//                unset($model->attributes['customergroup_price']);
//            });
//
//            $model->bindEvent('model.afterSave', function () use ($model, &$customergroupPrices)
//            {
//                if ($productRequest = \Request::input('Product'))
//                {
//                    $model->saveCustomergroupPrices(array_get($productRequest, 'customergroup_price', []));
//                }
//            });
//        });
//    }
//
//    public function getNameAttribute()
//    {
//        return $this->catalogitem_name;
//    }
//
//    public function setNameAttribute($value)
//    {
//        return $this->catalogitem_name = $value;
//    }
//
//    public function saveCustomergroupPrices($requestedItems)
//    {
//        /** @var Collection $existingItems */
//        $existingItems = CustomergroupProduct::where('product_id', $this->id)->get();
//
//        $requestedItems = collect($requestedItems)
//            ->filter(function ($price)
//            {
//                return $price > 0;
//            });
//
//        $existingItems->keyBy('customergroup_id')
//            ->diffKeys($requestedItems)
//            ->each(function ($item)
//            {
//                $item->delete();
//            });
//
//        foreach ($requestedItems as $requestedId => $requestedPrice)
//        {
//            if (!$saveItem = $existingItems->firstWhere('customergroup_id', $requestedId))
//            {
//                $saveItem = new CustomergroupProduct();
//
//                $saveItem->customergroup_id = $requestedId;
//                $saveItem->product_id = $this->id;
//            }
//
//            $saveItem->price = $requestedPrice * 100;
//
//            $saveItem->save();
//        }
//    }
//
//    public function getCustomergroupPriceAttribute()
//    {
//        return $this->product_customergroups
//            ->pluck('price', 'customergroup_id')
//            ->map(function ($price)
//            {
//                return $price / 100;
//            })
//            ->toArray();
//    }

}
