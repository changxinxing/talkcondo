<?php

namespace GFPDF_Vendor\Mpdf;

use GFPDF_Vendor\Mpdf\Color\ColorConverter;
use GFPDF_Vendor\Mpdf\Color\ColorModeConverter;
use GFPDF_Vendor\Mpdf\Color\ColorSpaceRestrictor;
use GFPDF_Vendor\Mpdf\Fonts\FontCache;
use GFPDF_Vendor\Mpdf\Fonts\FontFileFinder;
use GFPDF_Vendor\Mpdf\Image\ImageProcessor;
use GFPDF_Vendor\Mpdf\Pdf\Protection;
use GFPDF_Vendor\Mpdf\Pdf\Protection\UniqidGenerator;
use GFPDF_Vendor\Mpdf\Writer\BaseWriter;
use GFPDF_Vendor\Mpdf\Writer\BackgroundWriter;
use GFPDF_Vendor\Mpdf\Writer\ColorWriter;
use GFPDF_Vendor\Mpdf\Writer\BookmarkWriter;
use GFPDF_Vendor\Mpdf\Writer\FontWriter;
use GFPDF_Vendor\Mpdf\Writer\FormWriter;
use GFPDF_Vendor\Mpdf\Writer\ImageWriter;
use GFPDF_Vendor\Mpdf\Writer\JavaScriptWriter;
use GFPDF_Vendor\Mpdf\Writer\MetadataWriter;
use GFPDF_Vendor\Mpdf\Writer\OptionalContentWriter;
use GFPDF_Vendor\Mpdf\Writer\PageWriter;
use GFPDF_Vendor\Mpdf\Writer\ResourceWriter;
use Psr\Log\LoggerInterface;
class ServiceFactory
{
    public function getServices(\GFPDF_Vendor\Mpdf\Mpdf $mpdf, \Psr\Log\LoggerInterface $logger, $config, $restrictColorSpace, $languageToFont, $scriptToLanguage, $fontDescriptor, $bmp, $directWrite, $wmf)
    {
        $sizeConverter = new \GFPDF_Vendor\Mpdf\SizeConverter($mpdf->dpi, $mpdf->default_font_size, $mpdf, $logger);
        $colorModeConverter = new \GFPDF_Vendor\Mpdf\Color\ColorModeConverter();
        $colorSpaceRestrictor = new \GFPDF_Vendor\Mpdf\Color\ColorSpaceRestrictor($mpdf, $colorModeConverter, $restrictColorSpace);
        $colorConverter = new \GFPDF_Vendor\Mpdf\Color\ColorConverter($mpdf, $colorModeConverter, $colorSpaceRestrictor);
        $tableOfContents = new \GFPDF_Vendor\Mpdf\TableOfContents($mpdf, $sizeConverter);
        $cacheBasePath = $config['tempDir'] . '/mpdf';
        $cache = new \GFPDF_Vendor\Mpdf\Cache($cacheBasePath, $config['cacheCleanupInterval']);
        $fontCache = new \GFPDF_Vendor\Mpdf\Fonts\FontCache(new \GFPDF_Vendor\Mpdf\Cache($cacheBasePath . '/ttfontdata', $config['cacheCleanupInterval']));
        $fontFileFinder = new \GFPDF_Vendor\Mpdf\Fonts\FontFileFinder($config['fontDir']);
        $cssManager = new \GFPDF_Vendor\Mpdf\CssManager($mpdf, $cache, $sizeConverter, $colorConverter);
        $otl = new \GFPDF_Vendor\Mpdf\Otl($mpdf, $fontCache);
        $protection = new \GFPDF_Vendor\Mpdf\Pdf\Protection(new \GFPDF_Vendor\Mpdf\Pdf\Protection\UniqidGenerator());
        $writer = new \GFPDF_Vendor\Mpdf\Writer\BaseWriter($mpdf, $protection);
        $gradient = new \GFPDF_Vendor\Mpdf\Gradient($mpdf, $sizeConverter, $colorConverter, $writer);
        $formWriter = new \GFPDF_Vendor\Mpdf\Writer\FormWriter($mpdf, $writer);
        $form = new \GFPDF_Vendor\Mpdf\Form($mpdf, $otl, $colorConverter, $writer, $formWriter);
        $hyphenator = new \GFPDF_Vendor\Mpdf\Hyphenator($mpdf);
        $remoteContentFetcher = new \GFPDF_Vendor\Mpdf\RemoteContentFetcher($mpdf, $logger);
        $imageProcessor = new \GFPDF_Vendor\Mpdf\Image\ImageProcessor($mpdf, $otl, $cssManager, $sizeConverter, $colorConverter, $colorModeConverter, $cache, $languageToFont, $scriptToLanguage, $remoteContentFetcher, $logger);
        $tag = new \GFPDF_Vendor\Mpdf\Tag($mpdf, $cache, $cssManager, $form, $otl, $tableOfContents, $sizeConverter, $colorConverter, $imageProcessor, $languageToFont);
        $fontWriter = new \GFPDF_Vendor\Mpdf\Writer\FontWriter($mpdf, $writer, $fontCache, $fontDescriptor);
        $metadataWriter = new \GFPDF_Vendor\Mpdf\Writer\MetadataWriter($mpdf, $writer, $form, $protection, $logger);
        $imageWriter = new \GFPDF_Vendor\Mpdf\Writer\ImageWriter($mpdf, $writer);
        $pageWriter = new \GFPDF_Vendor\Mpdf\Writer\PageWriter($mpdf, $form, $writer, $metadataWriter);
        $bookmarkWriter = new \GFPDF_Vendor\Mpdf\Writer\BookmarkWriter($mpdf, $writer);
        $optionalContentWriter = new \GFPDF_Vendor\Mpdf\Writer\OptionalContentWriter($mpdf, $writer);
        $colorWriter = new \GFPDF_Vendor\Mpdf\Writer\ColorWriter($mpdf, $writer);
        $backgroundWriter = new \GFPDF_Vendor\Mpdf\Writer\BackgroundWriter($mpdf, $writer);
        $javaScriptWriter = new \GFPDF_Vendor\Mpdf\Writer\JavaScriptWriter($mpdf, $writer);
        $resourceWriter = new \GFPDF_Vendor\Mpdf\Writer\ResourceWriter($mpdf, $writer, $colorWriter, $fontWriter, $imageWriter, $formWriter, $optionalContentWriter, $backgroundWriter, $bookmarkWriter, $metadataWriter, $javaScriptWriter, $logger);
        return ['otl' => $otl, 'bmp' => $bmp, 'cache' => $cache, 'cssManager' => $cssManager, 'directWrite' => $directWrite, 'fontCache' => $fontCache, 'fontFileFinder' => $fontFileFinder, 'form' => $form, 'gradient' => $gradient, 'tableOfContents' => $tableOfContents, 'tag' => $tag, 'wmf' => $wmf, 'sizeConverter' => $sizeConverter, 'colorConverter' => $colorConverter, 'hyphenator' => $hyphenator, 'remoteContentFetcher' => $remoteContentFetcher, 'imageProcessor' => $imageProcessor, 'protection' => $protection, 'languageToFont' => $languageToFont, 'scriptToLanguage' => $scriptToLanguage, 'writer' => $writer, 'fontWriter' => $fontWriter, 'metadataWriter' => $metadataWriter, 'imageWriter' => $imageWriter, 'formWriter' => $formWriter, 'pageWriter' => $pageWriter, 'bookmarkWriter' => $bookmarkWriter, 'optionalContentWriter' => $optionalContentWriter, 'colorWriter' => $colorWriter, 'backgroundWriter' => $backgroundWriter, 'javaScriptWriter' => $javaScriptWriter, 'resourceWriter' => $resourceWriter];
    }
}
