<?php

namespace App\Pdf;

use Dompdf\Dompdf;

class PdfWithHeaderFooter extends Dompdf
{
    protected $headerHtml;
    protected $footerHtml;

    public function setHeaderHtml($html)
    {
        $this->headerHtml = $html;
    }

    public function setFooterHtml($html)
    {
        $this->footerHtml = $html;
    }

    public function header()
    {
        echo "Header HTML: " . $this->headerHtml;
        if ($this->headerHtml) {
            // Debugging statement
            echo "Header HTML: " . $this->headerHtml;
    
            $this->getCanvas()->text(72, 18, $this->headerHtml, null, 8, array(0, 0, 0));
        }
    }

    public function footer()
    {
        if ($this->footerHtml) {
            $this->getCanvas()->text(72, $this->getCanvas()->get_height() - 32, $this->footerHtml, null, 8, array(0, 0, 0));
        }
    }
}
