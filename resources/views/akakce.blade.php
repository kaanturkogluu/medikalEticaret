{!! '<?xml version="1.0" encoding="utf-8" standalone="yes"?>' !!}
<products xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
@foreach ($products as $product)
<product>
<sku>{{ $product->sku ?? $product->id }}</sku>
<name>{{ $product->name }}</name>
<url>{{ route('product.show', $product->slug ?? $product->id) }}</url>
@php
    $images = $product->images;
    $mainImage = $images->first();
    $additionalImages = $images->skip(1)->take(5);
@endphp
<imgUrl>{{ ($mainImage && !empty($mainImage->url)) ? asset($mainImage->url) : url('images/default-product.jpg') }}</imgUrl>
@if($additionalImages->count() > 0)
<additionalimages>
@foreach($additionalImages as $image)
@if(!empty($image->url))
<imgUrl>{{ asset($image->url) }}</imgUrl>
@endif
@endforeach
</additionalimages>
@endif
<description><![CDATA[{!! $product->description !!}]]></description>
<distributor></distributor>
<price>{{ number_format($product->price, 2, '.', '') }}</price>
<shipPrice>{{ $product->price < 700 ? '89.00' : '0.00' }}</shipPrice>
<dayOfDelivery>3</dayOfDelivery>
<expressDeliveryTime></expressDeliveryTime>
<quantity>{{ $product->stock }}</quantity>
<productBrand>{{ $product->brand_name ?? ($product->brand ? $product->brand->name : '') }}</productBrand>
<productCategory>{{ $product->category_name ?? ($product->category ? $product->category->name : '') }}</productCategory>
<barcode>{{ $product->barcode ?? '' }}</barcode>
</product>
@endforeach
</products>
