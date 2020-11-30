<?php

namespace App\Models;

use App\Http\Resources\MenuItemResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Translatable\HasTranslations;

class Menu extends Model
{
    use HasTranslations;

    const NAV_MENU = 1;
    const MENU_ITEM = 2;

    const ITEM_TARGET = [
        '_self',
        '_blank',
        '_parent',
        '_self',
        '_top',
    ];

    public $translatable = [
        'item_caption',
        'item_description',
    ];

    protected $fillable = [
        'menu_name',
        'item_caption',
        'item_description',
        'item_icon',
        'item_target',
        'item_url',
        'item_menu_id',
        'taxonomy',
        'item_parent',
        'order',
    ];
    
    public function scopeMenus($query)
    {
        return $query->where('taxonomy', Menu::NAV_MENU);
    }

    public function navItems()
    {
        return $this->hasMany(Menu::class, 'item_menu_id', 'id');
    }


    public static function buildTree($elements, int $parentId = 0)
    {
        $branch = array();

        foreach ($elements as &$element) {
            if ($element['item_parent'] == $parentId) {
                $children = self::buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[$element['id']] = $element;
                unset($element);
            }
        }

        usort($branch, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        return $branch;
    }

    public static function updateTree($elements, $original, int $parentId = null, array $ids = [])
    {
        $iteratedIds = [];
        foreach ($elements as &$element) {
            if($element['item_id'] === null) {
                $item = Menu::create([
                    'item_menu_id' => $element['item_menu_id'],
                    'taxonomy' => Menu::MENU_ITEM,
                    'item_parent' => $parentId,
                    'item_description' => $element['item_description'],
                    'item_icon' => $element['item_icon'],
                    'item_url' => $element['item_url'],
                    'item_caption' => $element['item_caption'],
                    'item_target' => $element['item_target'],
                    'order' => $element['order'],
                ]);
            }

            if( isset($element['children']) ? count($element['children']) : 0 >= 1 ) {
                $iteratedIds = array_merge(self::updateTree($element['children'], $original,  isset($item) ? $item->id : $element['item_id'], $iteratedIds), $iteratedIds);
            }
            if(!isset($item)) {
                $item = $original->navItems->where('id', $element['item_id'])->first();
            }
            array_push($iteratedIds, $item->id);
            $item->item_caption = $element['item_caption'];
            $item->item_target = $element['item_target'];
            $item->item_description = $element['item_description'];
            $item->item_icon = $element['item_icon'];
            $item->order = $element['order'];
            $item->item_parent = $parentId;
            $item->save();


            // variables need to delete at the and of iteration for not colliding in nested iteration
            unset($item);
            unset($element);
        }

        return $iteratedIds;
    }

}
