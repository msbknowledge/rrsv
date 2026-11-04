<?php
require_once('/libray/tcpdf/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(
    PDF_PAGE_ORIENTATION,
    PDF_UNIT,
    PDF_PAGE_FORMAT,
    true,
    'UTF-8',
    false
);

// set document information
$pdf->SetCreator('School');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Syllabus 2026');
$pdf->SetSubject('Bengali Syllabus');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set margins
$pdf->SetMargins(10, 10, 10);

// add page
$pdf->AddPage();

// ✅ Add Bengali Unicode font
$fontPath = __DIR__ . '/libray/bangla/NotoSansBengali-Regular.ttf';

$fontname = TCPDF_FONTS::addTTFfont(
    $fontPath,
    'TrueTypeUnicode',
    '',
    96
);

// set font
$pdf->SetFont($fontname, '', 12);

// Bengali content
$html = '
<h2>বাংলা সিলেবাস ২০২৬</h2>

<table border="1" cellpadding="5">
<tr>
    <th width="30%">Subject</th>
    <th width="20%">Unit</th>
    <th width="50%">Details</th>
</tr>

<tr>
    <td>বাংলা</td>
    <td>১ম ইউনিট</td>
    <td>
        প্রথম : বর্ণ পরিচয় : স্বরবর্ণ<br>
        দ্বিতীয় : বর্ণ পরিচয় : ব্যঞ্জনবর্ণ<br>
        তৃতীয় : বানান ব্যবহার<br>
        চতুর্থ : ব্যঞ্জন বর্ণ ব্যবহার
    </td>
</tr>

<tr>
    <td>English</td>
    <td>1st Unit</td>
    <td>Read and colour, Aa</td>
</tr>

</table>
';

// write HTML
$pdf->writeHTML($html, true, false, true, false, '');

// output PDF
$pdf->Output('syllabus.pdf', 'I');
?>
