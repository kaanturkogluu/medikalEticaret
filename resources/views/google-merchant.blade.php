{!! '<?xml version="1.0" encoding="utf-8"?>' !!}
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
  <channel>
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link>{{ url('/') }}</link>
    <description>{{ config('app.name', 'Laravel') }} - Google Merchant Feed</description>
    @foreach ($products as $product)
    <item>
      <g:id>{{ $product->sku ?? $product->id }}</g:id>
      <title><![CDATA[{{ $product->name }}]]></title>
      <description><![CDATA[{!! strip_tags($product->description) !!}]]></description>
      <link>{{ route('product.show', $product->slug ?? $product->id) }}</link>
      
      @php
          $images = $product->images;
          $mainImage = $images->first();
          $additionalImages = $images->skip(1)->take(5);
      @endphp
      <g:image_link>{{ ($mainImage && !empty($mainImage->url)) ? asset($mainImage->url) : url('images/default-product.jpg') }}</g:image_link>
      @if($additionalImages->count() > 0)
          @foreach($additionalImages as $image)
              @if(!empty($image->url))
              <g:additional_image_link>{{ asset($image->url) }}</g:additional_image_link>
              @endif
          @endforeach
      @endif

      <g:availability>{{ $product->stock > 0 ? 'in_stock' : 'out_of_stock' }}</g:availability>
      <g:price>{{ number_format($product->price, 2, '.', '') }} TRY</g:price>
      @if($product->brand_name || $product->brand)
      <g:brand><![CDATA[{{ $product->brand_name ?? $product->brand->name }}]]></g:brand>
      @endif
      
      @if($product->barcode)
      <g:gtin>{{ $product->barcode }}</g:gtin>
      @endif
      
      @if($product->category_name || $product->category)
      <g:product_type><![CDATA[{{ $product->category_name ?? $product->category->name }}]]></g:product_type>
      @endif

      <g:condition>new</g:condition>
    </item>
    @endforeach
  </channel>
</rss>
