@extends('layouts.dashboard')

@section('title', __('Blog Comments'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">{{ __('Blogs') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Comments') }}</li>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

<div class="card modern-card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="chart-icon-wrapper bg-info-subtle">
                    <i class="fas fa-comments text-info"></i>
                </div>
                <div>
                    <p class="text-muted text-uppercase small fw-semibold mb-1">{{ __('Blog Management') }}</p>
                    <h5 class="fw-bold mb-0 text-gray-900">{{ __('Comments for') }}: {{ $blog->translate(app()->getLocale())->title ?? $blog->translate('en')->title ?? __('Blog Post') }}</h5>
                </div>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-success-subtle text-success px-3 py-2">
                    <i class="fas fa-check-circle me-1"></i>{{ __('Approved') }}: {{ $approvedCount }}
                </span>
                <span class="badge bg-warning-subtle text-warning px-3 py-2">
                    <i class="fas fa-clock me-1"></i>{{ __('Pending') }}: {{ $pendingCount }}
                </span>
            </div>
        </div>
    </div>
</div>

@if($comments->count() > 0)
    <form id="bulkActionsForm" method="POST">
        @csrf
        <div class="card modern-card shadow-sm border-0 mb-3">
            <div class="card-body px-4 pb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                            <i class="fas fa-check-square me-1"></i>{{ __('Select All') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                            <i class="fas fa-square me-1"></i>{{ __('Deselect All') }}
                        </button>
                    </div>
                    <div>
                        <button type="submit" formaction="{{ route('admin.blogs.comments.bulk-approve', $blog) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-check me-1"></i>{{ __('Approve Selected') }}
                        </button>
                        <button type="submit" formaction="{{ route('admin.blogs.comments.bulk-delete', $blog) }}" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                            <i class="fas fa-trash me-1"></i>{{ __('Delete Selected') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endif

@forelse($comments->get('null', $comments->get(null, collect())) as $comment)
    <div class="card modern-card shadow-sm border-0 mb-3">
        <div class="card-body px-4 pb-4">
            <div class="d-flex align-items-start gap-3">
                <div class="form-check">
                    <input class="form-check-input comment-checkbox" type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" form="bulkActionsForm">
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $comment->name }}</h6>
                            <small class="text-muted">
                                <i class="fas fa-envelope me-1"></i>{{ $comment->email }}
                                @if($comment->website)
                                    | <i class="fas fa-globe me-1"></i><a href="{{ $comment->website }}" target="_blank">{{ $comment->website }}</a>
                                @endif
                                | <i class="fas fa-clock me-1"></i>{{ $comment->created_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div>
                            @if($comment->is_approved)
                                <span class="badge bg-success-subtle text-success">
                                    <i class="fas fa-check-circle me-1"></i>{{ __('Approved') }}
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning">
                                    <i class="fas fa-clock me-1"></i>{{ __('Pending') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <p class="mb-3" style="color: #6b7280; line-height: 1.7;">{{ $comment->comment }}</p>
                    <div class="d-flex gap-2">
                        @if(!$comment->is_approved)
                            <form action="{{ route('admin.blog-comments.approve', $comment) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-check me-1"></i>{{ __('Approve') }}
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.blog-comments.reject', $comment) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="fas fa-times me-1"></i>{{ __('Reject') }}
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.blog-comments.destroy', $comment) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                    
                    {{-- Replies --}}
                    @if($comment->replies->count() > 0)
                        <div class="mt-4 ps-4 border-start border-3 border-primary">
                            <h6 class="mb-3"><i class="fas fa-reply me-1"></i>{{ __('Replies') }} ({{ $comment->replies->count() }})</h6>
                            @foreach($comment->replies as $reply)
                                <div class="card mb-2" style="background: #f8f9fa;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1 fw-bold">{{ $reply->name }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>{{ $reply->email }}
                                                    | <i class="fas fa-clock me-1"></i>{{ $reply->created_at->format('d M Y, H:i') }}
                                                </small>
                                            </div>
                                            <div>
                                                @if($reply->is_approved)
                                                    <span class="badge bg-success-subtle text-success">{{ __('Approved') }}</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">{{ __('Pending') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="mb-2" style="color: #6b7280; line-height: 1.7;">{{ $reply->comment }}</p>
                                        <div class="d-flex gap-2">
                                            @if(!$reply->is_approved)
                                                <form action="{{ route('admin.blog-comments.approve', $reply) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success btn-sm">
                                                        <i class="fas fa-check me-1"></i>{{ __('Approve') }}
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.blog-comments.reject', $reply) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning btn-sm">
                                                        <i class="fas fa-times me-1"></i>{{ __('Reject') }}
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.blog-comments.destroy', $reply) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger btn-sm">
                                                    <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="card modern-card shadow-sm border-0">
        <div class="card-body px-4 pb-4 text-center py-5">
            <i class="fas fa-comments text-muted" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <h5 class="text-muted">{{ __('No comments found') }}</h5>
            <p class="text-muted">{{ __('This blog post has no comments yet.') }}</p>
        </div>
    </div>
@endforelse

<script>
function selectAll() {
    document.querySelectorAll('.comment-checkbox').forEach(cb => cb.checked = true);
}

function deselectAll() {
    document.querySelectorAll('.comment-checkbox').forEach(cb => cb.checked = false);
}
</script>
@endsection

