@extends('layouts.dashboard')

@section('title', __('Affiliate Program'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Affiliate') }}</li>
@endsection

@section('content')
@php($currentLocale = app()->getLocale())
@php($pagedSales = $affiliate && $sales instanceof \Illuminate\Contracts\Pagination\Paginator ? collect($sales->items()) : collect())
@php($pageTotals = [
    'count' => $pagedSales->count(),
    'amount' => $pagedSales->sum('sale_amount'),
    'commission' => $pagedSales->sum('commission_amount'),
])


@endsection
