<?php

use App\Media;
use App\Product;
use Illuminate\Database\Migrations\Migration;

class CopyProductImagesAsVariationImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $products = Product::whereNotNull('image')
                        ->with(['variations'])
                        ->get();

        $media_data = [];
        foreach ($products as $product) {
            if (empty($product->image)) {
                continue;
            }
            $source = public_path('uploads/' . config('constants.product_img_path')) . '/' . $product->image;
            $dest = public_path('uploads/media/' . $product->image);

            if (file_exists($source)) {
                copy($source, $dest);
                foreach ($product->variations as $variation) {
                    $media_data[] = [
                        'business_id' => $product->business_id,
                        'file_name' => $product->image,
                        'model_id' => $variation->id,
                        'model_type' => 'App\Variation',
                        'created_at' => $variation->created_at,
                        'updated_at' => $variation->updated_at
                    ];
                }
            }
        }

        if (!empty($media_data)) {
            Media::insert($media_data);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
