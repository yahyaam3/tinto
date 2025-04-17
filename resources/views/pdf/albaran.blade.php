<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Albarán {{ $albaran->numero }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a202c;
            margin: 0;
        }
        .info {
            margin-bottom: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-section {
            padding: 15px;
            background-color: #f7fafc;
            border-radius: 5px;
        }
        .info-section h2 {
            color: #2d3748;
            margin-top: 0;
            font-size: 16px;
        }
        .info-section p {
            margin: 5px 0;
            color: #4a5568;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f7fafc;
            font-weight: bold;
            color: #4a5568;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            margin-top: 20px;
        }
        .estado {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
        }
        .estado-pendiente { background-color: #fefcbf; color: #975a16; }
        .estado-en_proceso { background-color: #bee3f8; color: #2c5282; }
        .estado-completado { background-color: #c6f6d5; color: #276749; }
        .estado-entregado { background-color: #e9d8fd; color: #553c9a; }
        .observaciones {
            margin-top: 30px;
            padding: 15px;
            background-color: #f7fafc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Albarán {{ $albaran->numero }}</h1>
        <p>Fecha: {{ \Carbon\Carbon::parse($albaran->fecha)->format('d/m/Y') }}</p>
        <div class="estado estado-{{ $albaran->estado }}">
            {{ ucfirst($albaran->estado) }}
        </div>
    </div>

    <div class="info">
        <div class="info-grid">
            <div class="info-section">
                <h2>Información del Cliente</h2>
                <p><strong>Nombre:</strong> {{ $albaran->cliente->nombre }}</p>
                <p><strong>Teléfono:</strong> {{ $albaran->cliente->telefono }}</p>
                <p><strong>Email:</strong> {{ $albaran->cliente->email }}</p>
                <p><strong>Dirección:</strong> {{ $albaran->cliente->direccion }}</p>
            </div>
            <div class="info-section">
                <h2>Información del Albarán</h2>
                <p><strong>Número:</strong> {{ $albaran->numero }}</p>
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($albaran->fecha)->format('d/m/Y') }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($albaran->estado) }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($albaran->productos as $producto)
            <tr>
                <td>{{ $producto->nombre }}</td>
                <td>{{ $producto->pivot->cantidad }}</td>
                <td>{{ number_format($producto->precio, 2, ',', '.') }}€</td>
                <td>{{ number_format($producto->precio * $producto->pivot->cantidad, 2, ',', '.') }}€</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: {{ number_format($albaran->total, 2, ',', '.') }}€
    </div>

    @if($albaran->observaciones)
    <div class="observaciones">
        <h2>Observaciones</h2>
        <p>{{ $albaran->observaciones }}</p>
    </div>
    @endif
</body>
</html> 