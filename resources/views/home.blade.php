@php

/**
 * @var $this Illuminate\View\Engines\CompilerEngine
 * @var $vueComponent string
 * @var $usersTotal int
 * @var $departmentsTotal int
 * @var $departmentsWithUsers int
 * @var $unlinkedUsers int
 * @var $lastUpdate string
 */

$title = !empty($vueComponent) ? __(ucfirst($vueComponent)) : config('app.name', 'Laravel');
$vueComponents = ['departments', 'users', 'add-department', 'edit-department', 'add-user', 'edit-user'];

@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @if(empty($vueComponent) || !in_array($vueComponent, $vueComponents))
            <div class="col-md-8">
                <div class="card home-page">
                    <div class="card-header">{{ __('Statistics') }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <ul class="statistics list-group">
                            @isset($usersTotal)
                            <li class="list-group-item">
                                <b>{{ __('Total users') }}</b>
                                <span class="badge badge-primary badge-pill">{{ $usersTotal }}</span>
                            </li>
                            @endisset
                            @isset($unlinkedUsers)
                            <li class="list-group-item">
                                <b>{{ __('Unlinked users') }}</b>
                                <span class="badge badge-primary badge-pill">{{ $unlinkedUsers }}</span>
                            </li>
                            @endisset
                            @isset($departmentsTotal)
                            <li class="list-group-item">
                                <b>{{ __('Total departments') }}</b>
                                <span class="badge badge-primary badge-pill">{{ $departmentsTotal }}</span>
                            </li>
                            @endisset
                            @isset($departmentsWithUsers)
                            <li class="list-group-item">
                                <b>{{ __('Departments with users') }}</b>
                                <span class="badge badge-primary badge-pill">{{ $departmentsWithUsers }}</span>
                            </li>
                            @endisset
                            @isset($lastUpdate)
                            <li class="list-group-item">
                                <b>{{ __('Last update') }}</b>
                                <span class="badge badge-primary badge-pill">{{ $lastUpdate }}</span>
                            </li>
                            @endisset
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-10">
                <div id="vue_app">
                    <app-component></app-component>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
