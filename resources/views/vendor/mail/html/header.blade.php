@props(['url'])
<tr>
<td class="header" style="text-align: center; padding: 20px 0;">
    <a href="{{ $url }}" style="display: inline-block;">
        @php
            $logoPath = public_path('images/logo_fona.png');
        @endphp

        {{-- Validamos que exista $message (contexto de correo) y el archivo físico --}}
        @if(isset($message) && file_exists($logoPath))
            <img src="{{ $message->embed(public_path('images/logo_fona.png')) }}" alt="Logo FONA" style="height: 80px;">
        @else
            {{-- Si no hay $message, mostramos texto para evitar el error --}}
            <h1 style="color: #0056b3;">SIGH - FONA</h1>
        @endif
    </a>
</td>
</tr>
