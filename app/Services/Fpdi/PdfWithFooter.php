<?php

namespace App\Services\Fpdi;

use setasign\Fpdi\Fpdi;

class PdfWithFooter extends Fpdi
{
    public string $docNumber = '';
    public string $docDate   = '';

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Times', '', 8);

        $w = $this->w - $this->lMargin - $this->rMargin;

        $this->Cell($w * 0.15, 6, 'Nomor', 1, 0, 'C');
        $this->Cell($w * 0.25, 6, $this->docNumber, 1, 0, 'C');
        $this->Cell($w * 0.15, 6, 'Tanggal', 1, 0, 'C');
        $this->Cell($w * 0.20, 6, $this->docDate, 1, 0, 'C');
        $this->Cell($w * 0.10, 6, 'Halaman', 1, 0, 'C');
        $this->Cell($w * 0.15, 6, $this->PageNo() . ' dari {nb}', 1, 0, 'C');
    }
}
