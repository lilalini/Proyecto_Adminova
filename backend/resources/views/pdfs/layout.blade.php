<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background-color: #dee2e6; }
        th, td { border: 1px solid #cfd2dc; padding: 8px; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total { font-weight: bold; background-color: #f8f9fa; }
        .footer-note { border-top: 1px solid #999; font-size: 10px; text-align: center; }
        .doc-header { width: 100%; border: none; margin-bottom: 20px; }
        .doc-header td { border: none; padding: 0; vertical-align: middle; }
    </style>
</head>
<body>

<!-- Footer en todas las páginas -->
<htmlpagefooter name="main-footer">
    <div class="footer-note">
        Página {PAGENO} / {nbpg}
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="main-footer" value="on" />

<!-- Cabecera de documento (logo + título específico de cada doc) -->
<table class="doc-header">
    <tr>
        <td style="width: 220px; text-align: center;">
            <img src="{{ public_path('images/logo.png') }}" width="200" alt="Logo" />
            
        </td>
        <td style="text-align: right; vertical-align: middle;">
            @yield('doc-title')
        </td>
    </tr>
</table>

<hr style="border: 1px solid #dee2e6; margin-bottom: 20px;">

<!-- Contenido -->
@yield('content')

</body>
</html>