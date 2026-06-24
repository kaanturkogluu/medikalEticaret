{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<products>
    @foreach ($products as $product)
    <product>
        <productId>{{ $product->sku ?? $product->id }}</productId>
        <name><![CDATA[{{ $product->name }}]]></name>
        <barcode>{{ $product->barcode ?? '' }}</barcode>
        <price>{{ number_format($product->price, 2, '.', '') }}</price>
        <stock>{{ $product->stock }}</stock>
        <category><![CDATA[{{ $product->category_name ?? ($product->category ? $product->category->name : '') }}]]></category>
        
        @php
            $mainImage = $product->images->first();
        @endphp
        <imageUrl><![CDATA[{{ $mainImage ? asset('storage/' . $mainImage->image_path) : url('images/default-product.jpg') }}]]></imageUrl>
        
        <brand><![CDATA[{{ $product->brand_name ?? ($product->brand ? $product->brand->name : '') }}]]></brand>
        
        <url><![CDATA[{{ route('product.show', $product->slug ?? $product->id) }}]]></url>
    </product>
    @endforeach
</products>
