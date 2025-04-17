<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura #{{ $factura->numero }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .header {
            margin-bottom: 30px;
        }
        .empresa-nombre {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .empresa-direccion {
            font-size: 11px;
            line-height: 1.2;
        }
        .documento-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
            width: 300px;
            float: left;
        }
        .cliente-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
            width: 300px;
            float: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            clear: both;
        }
        th, td {
            border-bottom: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        .albaran-col { width: 15%; }
        .descripcion-col { width: 40%; }
        .cantidad-col { width: 15%; text-align: center; }
        .precio-col { width: 15%; text-align: right; }
        .total-col { width: 15%; text-align: right; }
        
        .totales {
            width: 200px;
            float: right;
            margin-top: 20px;
            text-align: right;
        }
        .totales div {
            margin: 5px 0;
        }
        .total-final {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .pagado-sello {
            position: absolute;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%) rotate(-15deg);
            color: #ff0000;
            border: 2px solid #ff0000;
            padding: 5px 20px;
            font-size: 24px;
            font-weight: bold;
            opacity: 0.7;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 10px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="empresa-nombre">TINTORERIA 7'S PRO-ECOLOGICA</div>
        <div class="empresa-direccion">
            Gravina, 35<br>
            17480 Roses<br>
            Girona<br>
            Tel.:972152202 Fax:<br>
            Email: tintoreriaecorosses@gmail.com
        </div>
    </div>

    <div class="documento-box">
        <div>
            <strong>Nº FACTURA</strong>
            <span style="margin-left: 20px">EJERCICI</span>
            <span style="margin-left: 20px">DATA</span>
        </div>
        <div style="margin-top: 5px">
            <strong>{{ $factura->numero }}</strong>
            <span style="margin-left: 40px">{{ date('y', strtotime($factura->fecha)) }}</span>
            <span style="margin-left: 20px">{{ date('d/m/Y', strtotime($factura->fecha)) }}</span>
        </div>
    </div>

    <div class="cliente-box">
        <div>{{ $factura->cliente->nombre }}</div>
        <div>{{ $factura->cliente->direccion }}</div>
        <div>{{ $factura->cliente->poblacion }}</div>
        @if($factura->cliente->nif)
        <div>N.I.F.: {{ $factura->cliente->nif }}</div>
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Albarán</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
                <tr>
                    <td>{{ $producto['albaran_numero'] }}</td>
                    <td>{{ $producto['nombre'] }}</td>
                    <td class="text-right">{{ number_format($producto['cantidad'], 2) }}</td>
                    <td class="text-right">{{ number_format($producto['precio_unitario'], 2) }}€</td>
                    <td class="text-right">{{ number_format($producto['subtotal'], 2) }}€</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <div>SUBTOTAL: {{ number_format($factura->subtotal, 2) }} €</div>
        <div>DTE.: 0%</div>
        <div>I.V.A.: {{ number_format($factura->iva, 2) }} €</div>
        <div class="total-final">
            <strong>TOTAL FACTURA: {{ number_format($factura->total, 2) }} €</strong>
        </div>
    </div>

    @if($factura->estado === 'pagada')
    <div class="pagado-sello">PAGAT</div>
    @endif

    <div class="footer">
        MARIA TERESA VILA GABARRÓ N.I.F.: 40510550Y
    </div>
</body>
</html> 