<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    /**
     * Display comments for a specific blog
     */
    public function index(Blog $blog)
    {
        $allComments = $blog->allComments()
            ->with('replies')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group comments by parent_id (null for top-level comments)
        $comments = $allComments->groupBy(function($comment) {
            return $comment->parent_id ?? 'null';
        });

        $approvedCount = $blog->allComments()->where('is_approved', true)->count();
        $pendingCount = $blog->allComments()->where('is_approved', false)->count();

        return view('admin.cms.blogs.comments', compact('blog', 'comments', 'approvedCount', 'pendingCount'));
    }

    /**
     * Approve a comment
     */
    public function approve(BlogComment $comment)
    {
        $comment->update(['is_approved' => true]);

        return redirect()->back()
            ->with('success', __('Comment approved successfully.'));
    }

    /**
     * Reject/Unapprove a comment
     */
    public function reject(BlogComment $comment)
    {
        $comment->update(['is_approved' => false]);

        return redirect()->back()
            ->with('success', __('Comment rejected successfully.'));
    }

    /**
     * Delete a comment
     */
    public function destroy(BlogComment $comment)
    {
        // Delete all replies first
        $comment->replies()->delete();
        
        $comment->delete();

        return redirect()->back()
            ->with('success', __('Comment deleted successfully.'));
    }

    /**
     * Bulk approve comments
     */
    public function bulkApprove(Request $request, Blog $blog)
    {
        $commentIds = $request->input('comment_ids', []);
        
        BlogComment::whereIn('id', $commentIds)
            ->where('blog_id', $blog->id)
            ->update(['is_approved' => true]);

        return redirect()->back()
            ->with('success', __('Comments approved successfully.'));
    }

    /**
     * Bulk delete comments
     */
    public function bulkDelete(Request $request, Blog $blog)
    {
        $commentIds = $request->input('comment_ids', []);
        
        BlogComment::whereIn('id', $commentIds)
            ->where('blog_id', $blog->id)
            ->delete();

        return redirect()->back()
            ->with('success', __('Comments deleted successfully.'));
    }
}
