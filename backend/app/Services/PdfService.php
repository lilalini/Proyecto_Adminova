<?php

namespace App\Services;

use Mpdf\Mpdf;
use App\Models\Booking;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    protected Mpdf $mpdf;

    public function __construct()
    {
        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 24,
            'margin_bottom' => 16,
            'margin_left' => 15,
            'margin_right' => 15,
            'default_font' => 'dejavusans',
        ]);
    }

    /**
     * Renderiza una vista Blade y devuelve el PDF como string
     * Método genérico para cualquier vista
     */
    public function render(string $view, array $data = []): string
    {
        $html = view($view, $data)->render();
        $this->mpdf->WriteHTML($html);
        return $this->mpdf->Output('', 'S');
    }

    /**
     * genera factura de reserva (usando vista pdfs.invoice)
     */
    public function generateInvoice(Booking $booking): string
    {
        return $this->render('pdfs.invoice', compact('booking'));
    }

    /**
     * genera contrato de alojamiento (usando vista pdfs.contract)
     */
    public function generateContract(array $data): string
    {
        return $this->render('pdfs.contract', $data);
    }

    /**
     * guarda pdf en storage y crea registro en documents
     */
    public function saveAndRegister(
        string $pdfContent, 
        string $documentType, 
        $documentable, 
        string $title
    ): Document {
        $path = 'documents/' . date('Y/m') . '/' . uniqid() . '.pdf';
        Storage::disk('public')->put($path, $pdfContent);

        return Document::create([
            'documentable_type' => get_class($documentable),
            'documentable_id' => $documentable->id,
            'document_type' => $documentType,
            'title' => $title,
            'file_name' => $title . '.pdf',
            'file_path' => $path,
            'file_size' => Storage::disk('public')->size($path),
            'mime_type' => 'application/pdf',
            'is_verified' => true,
        ]);
    }

    /**
     * descarga el pdf directamente (para confirmaciones, etc.)
     */
    public function download(string $view, array $data, string $filename)
    {
        $pdf = $this->render($view, $data);
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"');
    }
}