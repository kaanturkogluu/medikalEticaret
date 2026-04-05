<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return back()->with('error', 'Yorum yapabilmek için giriş yapmalısınız.');
        }

        // Spam prevention: Check if user has a pending comment for this product
        $pendingComment = Comment::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('is_approved', false)
            ->first();

        if ($pendingComment) {
            return back()->with('error', 'Bu ürün için henüz onay bekleyen bir yorumunuz bulunuyor. Lütfen önce onun onaylanmasını bekleyin.');
        }

        $request->validate([
            'content' => 'required|min:5|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
            'is_approved' => false, // Onay bekliyor
        ]);

        return back()->with('success', 'Yorumunuz alındı. Onaylandıktan sonra yayınlanacaktır. Teşekkürler!');
    }
}
