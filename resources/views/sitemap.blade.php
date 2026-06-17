<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    @foreach ($products as $product)
        <url>
            <loc>{{ route('product.show', $product->slug ?? $product->id) }}</loc>
            <lastmod>{{ $product->updated_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
    @foreach ($categories as $category)
        <url>
            <loc>{{ route('home', ['category' => $category->slug ?? $category->id]) }}</loc>
            <lastmod>{{ $category->updated_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
    @foreach ($brands as $brand)
        <url>
            <loc>{{ route('home', ['brand' => $brand->slug ?? $brand->id]) }}</loc>
            <lastmod>{{ $brand->updated_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach
    @foreach ($pages as $page)
        <url>
            <loc>{{ route('page.show', $page->slug ?? $page->id) }}</loc>
            <lastmod>{{ $page->updated_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.5</priority>
        </url>
    @endforeach
</urlset>
