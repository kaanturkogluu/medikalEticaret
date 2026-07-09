<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Http\Resources\V1\BannerResource;
use App\Http\Resources\V1\CategoryResource;

class ShopController extends Controller
{
    public function banners()
    {
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        return BannerResource::collection($banners);
    }

    public function categories()
    {
        // Get only active parent categories with their active children
        $categories = Category::with(['children' => function($q) {
            $q->where('active', true);
        }])
        ->whereNull('parent_id')
        ->where('active', true)
        ->get();
        
        return CategoryResource::collection($categories);
    }
}
