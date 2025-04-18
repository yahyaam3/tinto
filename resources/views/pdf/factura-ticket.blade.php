<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $factura->numero }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html {
            height: 100%;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            padding: 20mm;
            background: white;
            min-height: 100%;
            max-height: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .contenedor {
            flex: 1;
            position: relative;
            height: auto;
        }
        .header {
            margin-bottom: 30px;
        }
        .empresa-info {
            float: left;
        }
        .empresa-nombre {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .empresa-detalle {
            line-height: 1.5;
        }
        .factura-info {
            float: right;
            text-align: right;
        }
        .numero-factura {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .cliente-info {
            clear: both;
            margin: 40px 0;
            padding: 15px 0;
        }
        .cliente-titulo {
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            border-bottom: 1px solid #000;
            padding: 8px;
            text-align: left;
            text-transform: uppercase;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .albaran { width: 15%; }
        .descripcion { width: 45%; }
        .cantidad { width: 13%; text-align: right; }
        .precio { width: 13%; text-align: right; }
        .total-linea { width: 14%; text-align: right; }
        .totales {
            width: 250px;
            float: right;
            margin-top: 20px;
            text-align: right;
        }
        .totales div {
            margin: 5px 0;
        }
        .total-final {
            border-top: 1px solid #000;
            margin-top: 5px;
            padding-top: 5px;
            font-weight: bold;
        }
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 11px;
            padding: 20px 0;
        }
        .print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            z-index: 1000;
        }
        .print-button:hover {
            background: #000;
        }
        @media print {
            html, body {
                height: 297mm;
                width: 210mm;
                margin: 0;
            }
            body {
                padding: 20mm;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="header clearfix">
            <div class="empresa-info">
                <div class="empresa-nombre">TINTORERIA 7'S PRO-ECOLOGICA</div>
                <div class="empresa-detalle">
                    Maria Teresa Vila Gabarro<br>
                    NIF: 40510501Y<br>
                    Gravina, 35<br>
                    17480 Roses (Girona)<br>
                    Tel.: 972152202
                </div>
            </div>
            <div class="factura-info">
                <div class="numero-factura">Factura Nº {{ $factura->numero }}</div>
                <div>Fecha: {{ $factura->fecha->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="cliente-info">
            <div class="cliente-titulo">DATOS DEL CLIENTE</div>
            <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px; font-weight: bold;">Cliente:</td>
                    <td style="padding: 5px;">{{ $factura->cliente->nombre }}</td>
                </tr>
                @if($factura->cliente->nif)
                <tr>
                    <td style="padding: 5px; font-weight: bold;">NIF:</td>
                    <td style="padding: 5px;">{{ $factura->cliente->nif }}</td>
                </tr>
                @endif
                @if($factura->cliente->telefono)
                <tr>
                    <td style="padding: 5px; font-weight: bold;">Teléfono:</td>
                    <td style="padding: 5px;">{{ $factura->cliente->telefono }}</td>
                </tr>
                @endif
                @if($factura->cliente->direccion)
                <tr>
                    <td style="padding: 5px; font-weight: bold;">Dirección:</td>
                    <td style="padding: 5px;">{{ $factura->cliente->direccion }}</td>
                </tr>
                @endif
            </table>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="albaran">ALBARÁN</th>
                    <th class="descripcion">DESCRIPCIÓN</th>
                    <th class="cantidad">CANTIDAD</th>
                    <th class="precio">PRECIO UNIT.</th>
                    <th class="total-linea">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                <tr>
                    <td class="albaran">{{ $producto['albaran_numero'] }}</td>
                    <td class="descripcion">{{ $producto['nombre'] }}</td>
                    <td class="cantidad">{{ number_format($producto['cantidad'], 2) }}</td>
                    <td class="precio">{{ number_format($producto['precio_unitario'], 2) }} €</td>
                    <td class="total-linea">{{ number_format($producto['subtotal'], 2) }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totales">
            <div>Base Imponible: {{ number_format($factura->subtotal, 2) }} €</div>
            <div>IVA (21%): {{ number_format($factura->iva, 2) }} €</div>
            <div class="total-final">TOTAL: {{ number_format($factura->total, 2) }} €</div>
        </div>

        <div class="footer">
            <div>Gracias por su confianza</div>
            <div style="margin-top: 5px;">Este documento sirve como justificante legal de la prestación del servicio</div>
        </div>
    </div>

    <button onclick="window.print()" class="print-button">Imprimir Factura</button>
</body>
</html> 