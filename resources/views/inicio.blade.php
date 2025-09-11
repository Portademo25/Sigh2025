@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $mensaje }}</div>

                <div class="card-body">

                        <div class="card-text">
                            {{ $textoAdicional }}
                        </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
