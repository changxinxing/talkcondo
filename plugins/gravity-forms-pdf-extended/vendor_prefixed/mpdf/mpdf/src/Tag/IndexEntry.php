<?php

namespace GFPDF_Vendor\Mpdf\Tag;

class IndexEntry extends \GFPDF_Vendor\Mpdf\Tag\Tag
{
    public function open($attr, &$ahtml, &$ihtml)
    {
        if (!empty($attr['CONTENT'])) {
            if (!empty($attr['XREF'])) {
                $this->mpdf->IndexEntry(\htmlspecialchars_decode($attr['CONTENT'], \ENT_QUOTES), $attr['XREF']);
                return;
            }
            $objattr = [];
            $objattr['CONTENT'] = \htmlspecialchars_decode($attr['CONTENT'], \ENT_QUOTES);
            $objattr['type'] = 'indexentry';
            $objattr['vertical-align'] = 'T';
            $e = "»¤¬type=indexentry,objattr=" . \serialize($objattr) . "»¤¬";
            if ($this->mpdf->tableLevel) {
                $this->mpdf->cell[$this->mpdf->row][$this->mpdf->col]['textbuffer'][] = [$e];
            } else {
                // *TABLES*
                $this->mpdf->textbuffer[] = [$e];
            }
            // *TABLES*
        }
    }
    public function close(&$ahtml, &$ihtml)
    {
    }
}
