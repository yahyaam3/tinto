<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Albarán #{{ $albaran->numero }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            width: 80mm;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 5mm;
            width: 70mm;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .empresa-nombre {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .empresa-info {
            font-size: 12px;
            color: #000;
        }
        .linea-punteada {
            border-top: 1px dotted #000;
            margin: 8px 0;
            width: 100%;
        }
        .datos-albaran {
            margin: 8px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        th {
            text-align: left;
            font-weight: normal;
            padding-bottom: 5px;
        }
        td {
            padding: 2px 0;
        }
        .cantidad {
            width: 10%;
            text-align: right;
            padding-right: 5px;
        }
        .descripcion {
            width: 50%;
            text-align: left;
            padding-left: 5px;
        }
        .precio {
            width: 20%;
            text-align: right;
            padding-right: 5px;
        }
        .total-linea {
            width: 20%;
            text-align: right;
        }
        .totales {
            margin-top: 8px;
            text-align: right;
            font-size: 12px;
        }
        .totales div {
            margin: 3px 0;
        }
        .footer {
            margin-top: 15px;
            text-align: left;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="empresa-nombre">
            TINTORERIA 7'S PRO-ECOLOGICA<br>
            MARIA TERESA VILA GABARRO
        </div>
        <div class="empresa-info">
            40510501Y<br>
            Gravina, 35 TEL: 972152202
        </div>
    </div>

    <div class="linea-punteada"></div>

    <div class="datos-albaran">
        {{ date('d/m/Y') }}    REF: {{ $albaran->numero }}<br>
        CLIENTE: {{ $albaran->cliente->nombre }}<br>
        @if($albaran->cliente->direccion)
        DIR: {{ $albaran->cliente->direccion }}<br>
        @endif
        @if($albaran->cliente->telefono)
        TEL: {{ $albaran->cliente->telefono }}
        @endif
    </div>

    <div class="linea-punteada"></div>

    <table>
        <thead>
            <tr>
                <th class="cantidad">Cant</th>
                <th class="descripcion">Descripción</th>
                <th class="precio">P.Unit</th>
                <th class="total-linea">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($albaran->productos as $producto)
            <tr>
                <td class="cantidad">{{ number_format($producto->pivot->cantidad, 2) }}</td>
                <td class="descripcion">{{ strtolower($producto->nombre) }}</td>
                <td class="precio">{{ number_format($producto->pivot->precio_unitario, 2) }}</td>
                <td class="total-linea">{{ number_format($producto->pivot->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="linea-punteada"></div>

    <div class="totales">
        <div>Subtotal: {{ number_format($albaran->total, 2) }} €</div>
        <div>Descuento 0%: 0,00 €</div>
        <div>A pagar: {{ number_format($albaran->total, 2) }} €</div>
        <div>Entregado a cuenta: 0,00 €</div>
        <div style="margin-top: 5px; font-weight: bold;">
            Total Debe: {{ number_format($albaran->total, 2) }} €
        </div>
    </div>

    <div class="linea-punteada"></div>

    <div class="footer">
        Total piezas: {{ $albaran->productos->sum('pivot.cantidad') }}<br>
        {{ date('d/m/Y H:i') }}
    </div>
</body>
</html> 