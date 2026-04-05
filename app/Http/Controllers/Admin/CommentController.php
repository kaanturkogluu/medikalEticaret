<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['user', 'product'])->latest()->paginate(20);
        return view('admin.comments.index', compact('comments'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Yorum onaylandı.');
    }

    public function reply(Request $request, Comment $comment)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ]);

        $comment->update([
            'admin_reply' => $request->admin_reply,
            'replied_at' => now(),
            'is_approved' => true,
        ]);

        return back()->with('success', 'Yorum yanıtlandı.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Yorum silindi.');
    }
}
