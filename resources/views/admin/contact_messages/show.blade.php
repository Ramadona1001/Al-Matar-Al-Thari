@extends('layouts.dashboard')
@section('title', __('View Message'))
@section('content')
<div class="container py-4">
  <a href="{{ route('admin.contact_messages.index') }}" class="btn btn-link mb-3">&larr; {{ __('Back') }}</a>
  <div class="card p-4">
    <div class="row g-3">
      <div class="col-md-6"><strong>{{ __('Name') }}:</strong> {{ $message->name }}</div>
      <div class="col-md-6"><strong>{{ __('Email') }}:</strong> {{ $message->email }}</div>
      <div class="col-md-12"><strong>{{ __('Subject') }}:</strong> {{ $message->subject }}</div>
      <div class="col-md-12"><strong>{{ __('Message') }}:</strong><div class="mt-2">{{ $message->message }}</div></div>
      <div class="col-md-12 text-muted">{{ __('Received') }}: {{ $message->created_at->toDayDateTimeString() }}</div>
    </div>
  </div>
</div>
@endsection