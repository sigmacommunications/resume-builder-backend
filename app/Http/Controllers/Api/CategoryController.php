<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function index()
    {
		$categories = Category::with(['templates' => function($q) {
            $q->where('status', 'active');
        }])->where('status','active')->get();

        $userId = auth()->id();

        $data = $categories->map(function($cat) use ($userId) {
            return [
                'id'   => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'templates' => $cat->templates->map(function($tpl) use ($userId) {
                    $purchased = \App\Models\TemplatePurchase::where('user_id', $userId)
                        ->where('template_id', $tpl->id)
                        ->where('status', 'completed')
                        ->exists();

                    return [
                        'id'           => $tpl->id,
                        'heading'      => $tpl->heading,
                        'price'        => $tpl->price,
                        'image'        => $tpl->image,
                        'type'         => $tpl->type,
                        'key'          => $tpl->key,
                        'description'  => $tpl->description,
                        'is_purchased' => $purchased,
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }
}
