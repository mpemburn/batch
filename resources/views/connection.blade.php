@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Create or Edit Connections') }}</div>
                <div class="card-body no-margin">
                    <livewire:connection-edit />
                </div>
            </div>
        </div>
    </div>
@endsection
