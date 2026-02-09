@section('title', 'Inicio')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h4>Bienvenido al Sistema</h4>
                        <p>Esta es la página de inicio. Si ves esto, significa que no tienes un rol específico asignado.</p>

                        @auth
                        <p>
                            <strong>Tu usuario:</strong> {{ auth()->user()->name }}<br>
                            <strong>Email:</strong> {{ auth()->user()->email }}<br>
                            <strong>Roles:</strong>
                            @if(auth()->user()->roles->count() > 0)
                                @foreach(auth()->user()->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-warning">Sin rol asignado</span>
                            @endif
                        </p>
                        @endauth
                    </div>

                    @guest
                    <div class="text-center">
                        <p>Por favor, inicia sesión para acceder al sistema.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary">Iniciar Sesión</a>
                    </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
