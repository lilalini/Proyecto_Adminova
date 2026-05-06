<?php

namespace App\Services;

use Mpdf\Mpdf;
use App\Models\Booking;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Crea una nueva instancia de Mpdf con la configuración base
     */
    protected function createMpdf(): Mpdf
    {
        return new Mpdf([
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
     */
    public function render(string $view, array $data = []): string
    {
        $mpdf = $this->createMpdf();
        $html = view($view, $data)->render();
        $mpdf->WriteHTML($html);
        return $mpdf->Output('', 'S');
    }

    /**
     * Genera factura de reserva
     */
    public function generateInvoice(Booking $booking): string
    {
        return $this->render('pdfs.invoice', compact('booking'));
    }

    /**
     * Genera contrato de alojamiento
     */
    public function generateContract(array $data): string
    {
        return $this->render('pdfs.contract', $data);
    }

    /**
     * Guarda PDF en storage y crea registro en documents
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
     * Descarga el PDF directamente
     */
    public function download(string $view, array $data, string $filename)
    {
        $pdf = $this->render($view, $data);
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"');
    }
}