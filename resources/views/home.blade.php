@php

/**
 * @var $this Illuminate\View\Engines\CompilerEngine
 * @var $vueComponent string
 */

$title = !empty($vueComponent) ? __(ucfirst($vueComponent)) : config('app.name', 'Laravel');
$vueComponents = ['departments', 'users'];

@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @if(empty($vueComponent) || !in_array($vueComponent, $vueComponents))
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-10">
                <div id="app">
                    <{{ $vueComponent }}-component></{{ $vueComponent }}-component>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
