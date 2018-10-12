<?php

// Get Requested Listing
$listing = requested_listing();

// Require Listing Row
if (!empty($listing)) {
    // Listing PHotos
    if (function_exists('images')) {
        $images = images($idx, $db_idx, $listing, true);
        $listing['thumbnails'] = $images;
    }

    // Include FPDF Library
    require_once Settings::getInstance()->DIRS['LIB'] . 'fpdf/fpdf.php';

    // Create FPDF
    $pdf = new FPDF();
    $pdf->AddPage();

    $listing['AddressSubdivision'] = !empty($listing['AddressSubdivision']) ? $listing['AddressSubdivision'] : 'N/A';

    /* heading */
    $pdf->SetFont('Helvetica', 'B', 20);
    $pdf->Cell(10, 20, '$' . Format::number($listing['ListingPrice']) . ' - ' . $listing['Address'] . ', ' .$listing['AddressCity']);
    $pdf->Ln(6);
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell(10, 20, html_entity_decode(Lang::write('MLS_NUMBER') . $listing['ListingMLS']));
    $pdf->Ln(24);
    $pdf->Line(11, 30, 197, 30);

    // Listing Photos
    if (!empty($listing['ListingImage'])) {
        $pdf->Image(Format::thumbUrl($listing['ListingImage'], '350x200/f'), 110, 36, 90, 0);
    }
    if (!empty($listing['thumbnails'][1])) {
        $pdf->Image(Format::thumbUrl($listing['thumbnails'][1], '350x200/f'), 110, 96, 90, 0);
    }
    if (!empty($listing['thumbnails'][2])) {
        $pdf->Image(Format::thumbUrl($listing['thumbnails'][2], '350x200/f'), 110, 162, 90, 0);
    }

    /* summary */
    $pdf->SetFont('Helvetica', 'B', 13);
    $pdf->Write(1, '$' . Format::number($listing['ListingPrice']));
    $pdf->Ln(6);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(1, "{$listing['NumberOfBedrooms']} Bedroom, {$listing['NumberOfBathrooms']} Bathroom, " . (!empty($listing['NumberOfSqFt']) ? Format::number($listing['NumberOfSqFt']) . " sqft" : ""));
    $pdf->Ln(6);
    $pdf->Write(1, "{$listing['ListingType']}" . (!empty($listing['NumberOfAcres']) ? " on " . Format::number($listing['NumberOfAcres']) . " Acres" : ""));
    $pdf->Ln(12);
    $pdf->Write(1, "{$listing['AddressSubdivision']}, {$listing['AddressCity']}, {$listing['AddressState']}");
    $pdf->Ln(7);
    $pdf->MultiCell(90, 6, "{$listing['ListingRemarks']}", 0, 'L');

    $pdf->Ln(6);

/* Year Built */
    if (!empty($listing['YearBuilt'])) {
        $pdf->Write(1, "Built in {$listing['YearBuilt']}");
        $pdf->Ln(6);
    }

    if ($idx->getDetails()) :
        foreach ($idx->getDetails() as $details) :
            /* Data Collection */
            $data = array();

            /* Loop through fields */
            foreach ($details['fields'] as $field) {
                /* Make sure value is available */
                if (!isset($listing[$field['value']])) {
                    continue;
                }

                /* Add to Collection */
                $data[$field['value']] = $listing[$field['value']];

                /* Format if Needed */
                if (isset($field['format'])) {
                    $data[$field['value']] = tpl_format($data[$field['value']], $field['format']);
                }

                /* Unset Empty Values */
                if (empty($data[$field['value']])) {
                    unset($data[$field['value']]);
                }
            }

            /* Skip Data Group if there is no Data */
            if (empty($data)) {
                continue;
            }

            /* Set data column widths */
            $w = array(40,150);

            /* Set detail group heading */
            $pdf->Ln(6);
            $pdf->SetFont('Helvetica', 'B', 13);
            $pdf->Write(1, "{$details['heading']}");
            $pdf->Ln(6);

            /* Output Data */
            foreach ($details['fields'] as $field) :
                if (!isset($data[$field['value']])) {
                    continue;
                }

                    /* Set data key */
                    $pdf->SetFont('Arial', '', 12);
                    $field['title'] = html_entity_decode($field['title']); /* Convert Entities to Characters */
                    $pdf->Cell($w[0], 6, $field['title']);

                    /* Set data value */
                    $pdf->MultiCell($w[1], 6, $data[$field['value']]);
                    $pdf->Ln(1);
            endforeach;
        endforeach;
    endif;

    $pdf->SetFont('Arial', '', 10);

    /**
     * Compliance Agent / Office
     */
    if (!empty($_COMPLIANCE['details']['show_agent'])) {
        $agentoffice = $listing['ListingAgent'];
    }

    $agentoffice = !empty($agentoffice) ? $agentoffice . ', ' . $listing['ListingOffice'] : $listing['ListingOffice'];

    if (!empty($agentoffice)) {
        $pdf->Ln(2);
        $pdf->MultiCell(150, 3, 'This listing courtesy of ' . html_entity_decode($agentoffice));
    }

    // Last Updated Time
    $last_updated = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->lastUpdated($db_idx, $idx);

    /**
     * Compliance Disclaimer
     */
    if (!empty($_COMPLIANCE['disclaimer'])) {
        if (!empty($_COMPLIANCE['logo'])) {
            $pdf->Ln(5);

            $count = 1;
            foreach ($_COMPLIANCE['disclaimer'] as $disclaimer) {
                /* Prep Disclaimer */
                ob_start();
                eval(' ?>' . $disclaimer . '<?php ');
                $disclaimer = ob_get_clean();
                $disclaimer = strip_tags($disclaimer);

                /* Skip Empty Values */
                $disclaimer_trim = trim($disclaimer);
                if (empty($disclaimer_trim)) {
                    continue;
                }

                // Place MLS Logo
                if ($count == $_COMPLIANCE['logo_location']) {
                    $X = $pdf->GetX();
                    $Y = $pdf->GetY();
                    $pdf->Image($_COMPLIANCE['logo'], $X, $Y, $_COMPLIANCE['logo_width'], 0);
                }

                // Move File Pointer to Right of Image
                if ($count == $_COMPLIANCE['logo_location'] || (is_array($_COMPLIANCE['shift_paragraphs']) && in_array($count, $_COMPLIANCE['shift_paragraphs']))) {
                    $pdf->SetX($X + $_COMPLIANCE['logo_width'] + 5);
                }

                $pdf->MultiCell(0, 3, html_entity_decode($disclaimer));
                $pdf->Ln(3);

                $count++;
            }
        } else {
            $_COMPLIANCE['disclaimer'] = implode('', $_COMPLIANCE['disclaimer']);
            ob_start();
            eval(' ?>' . $_COMPLIANCE['disclaimer'] . '<?php ');
            $_COMPLIANCE['disclaimer'] = ob_get_clean();
            $_COMPLIANCE['disclaimer'] = strip_tags($_COMPLIANCE['disclaimer']);
            $pdf->Ln(2);

            //$pdf->MultiCell(0, 4, html_entity_decode($_COMPLIANCE['disclaimer']));

            //Compliance Borders
            $pdf->Line(10, $pdf->getY(), 100, $pdf->getY()); //Top Line
            $pdf->Line(10, $pdf->getY()+38, 100, $pdf->getY()+38); //Bottom Line
            $pdf->Line(10, $pdf->getY(), 10, $pdf->getY()+38); //Left Line
            $pdf->Line(100, $pdf->getY(), 100, $pdf->getY()+38); //Right Line
            $pdf->Image($_COMPLIANCE['disclaimer_print_icon_realtor'], 11, $pdf->getY()+1, 10, 0);
            $pdf->Image($_COMPLIANCE['disclaimer_print_icon_rdx'], 35, $pdf->getY()+1, 30, 0);
            $pdf->Image($_COMPLIANCE['disclaimer_print_icon_equal_house'], 86, $pdf->getY()+1, 12, 0);
            $pdf->Ln(13);
            $pdf->MultiCell(90, 3, html_entity_decode($_COMPLIANCE['disclaimer']));
        }
    }

    // Last Updated Time
    if (!empty($_COMPLIANCE['update_time'])) {
        $pdf->Ln(2);
        $pdf->Write(1, 'Listing information last updated on ' . date('F jS, Y \a\t g:ia T', strtotime($last_updated)));
    }

    /* Clean Output */
    ob_clean();

    /* Send PDF */
    $pdf->Output();

    /* Exit Script */
    exit;
} else {
    // 404 Header
    header('HTTP/1.1 404 NOT FOUND');

    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_keyw  = Lang::write('IDX_DETAILS_META_KEYWORDS_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_desc  = Lang::write('IDX_DETAILS_META_DESCRIPTION_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
}
