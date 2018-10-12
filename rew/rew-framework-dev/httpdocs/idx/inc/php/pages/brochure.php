<?php

// Get Requested Listing
$listing = requested_listing();

// Require Listing Row
if (!empty($listing)) {
    $URL = Settings::getInstance()->SETTINGS['URL'];
    $DURL = Settings::getInstance()->SETTINGS['URL_DEV_AUTH'];
    // Load Compliance for specific feed if commingled.
    if (isset($listing['ListingFeed'])) {
        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($listing['ListingFeed']);
    }

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

    // heading
    // Compliance - Ereb Listing Courtesy
    if (isset($_COMPLIANCE['details']['provider_first']) && $_COMPLIANCE['details']['provider_first']($listing)) {
        $pdf->SetFont('Helvetica', '', 10);
        if (!empty($_COMPLIANCE['details']['show_agent']) || !empty($_COMPLIANCE['details']['show_office'])) {
            if (!empty($_COMPLIANCE['details']['show_agent'])) {
                $agentoffice = $listing['ListingAgent'];
            }
            if (!empty($_COMPLIANCE['details']['show_office'])) {
                $agentoffice = !empty($agentoffice) ? $agentoffice . ', ' . $listing['ListingOffice'] : $listing['ListingOffice'];
            }
            if (!empty($agentoffice)) {
                $pdf->MultiCell(197, 6, (!empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : '') . html_entity_decode($agentoffice, ENT_COMPAT | ENT_HTML5, 'ISO-8859-1'));
                if (!empty($_COMPLIANCE['details']['show_office_phone'])) {
                    $pdf->MultiCell(154, 6, 'Office Phone #: ' . $listing['ListingOfficePhoneNumber']);
                }
            }
        }
        $pdf->Ln(6);
    }
    $pdf->SetFont('Helvetica', 'B', 20);
    $pdf->Cell(10, 20, '$' . Format::number($listing['ListingPrice']) . ' - ' . $listing['Address'] . ', ' .$listing['AddressCity']);
    $pdf->Ln(6);
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->Cell(10, 20, html_entity_decode(Lang::write('MLS_NUMBER') . $listing['ListingMLS'], ENT_COMPAT | ENT_HTML5, 'ISO-8859-1') . ' ' . (!empty($_COMPLIANCE['details']['show_office_in_header']) ? (isset($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Listing Office') . ' ' . $listing['ListingOffice'] : ''));
    $pdf->Ln(24);
    if (isset($_COMPLIANCE['details']['provider_first']) && $_COMPLIANCE['details']['provider_first']($listing)) {
        $pdf->Line(11, 35, 197, 35);
    } else {
        $pdf->Line(11, 30, 197, 30);
    }

    // Listing Photos
    if (!empty($listing['ListingImage'])) {
        $pdf->Image(str_replace($URL, $DURL, $listing['ListingImage']), 110, 36, 90, 0);
    }
    if (!empty($listing['thumbnails'][1])) {
        $pdf->Image(str_replace($URL, $DURL, $listing['thumbnails'][1]), 110, 96, 90, 0);
    }
    if (!empty($listing['thumbnails'][2])) {
        $pdf->Image(str_replace($URL, $DURL, $listing['thumbnails'][2]), 110, 162, 90, 0);
    }

    // summary
    $pdf->SetFont('Helvetica', 'B', 13);
    $pdf->Write(1, '$' . Format::number($listing['ListingPrice']));
    $pdf->Ln(6);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Write(1, "{$listing['NumberOfBedrooms']} Bedroom, {$listing['NumberOfBathrooms']} Bathroom, " . (!empty($listing['NumberOfSqFt']) ? Format::number($listing['NumberOfSqFt']) . Lang::write('IDX_BROCHURE_SQFT_TEXT') : ""));
    if (!empty($listing['DescriptionSqFtSource'])) {
        $pdf->Ln(6);
        $pdf->Write(1, "Sqft Source: {$listing['DescriptionSqFtSource']}");
    }
    $pdf->Ln(6);
    $pdf->Write(1, "{$listing['ListingType']}" . (!empty($listing['NumberOfAcres']) ? " on " . Format::number($listing['NumberOfAcres']) . " Acres" : ""));
    $pdf->Ln(12);
    $pdf->Write(1, "{$listing['AddressSubdivision']}, {$listing['AddressCity']}, {$listing['AddressState']}");
    $pdf->Ln(7);
    $pdf->MultiCell(90, 6, "{$listing['ListingRemarks']}", 0, 'L');

    $pdf->Ln(6);

    if ($_COMPLIANCE['details']['show_below_remarks']) {
        if (!empty($_COMPLIANCE['details']['lang']['provider_bold'])) {
            $pdf->SetFont('Arial', 'B');
        }

        if (!empty($_COMPLIANCE['provider']['logo'])) {
            // Place MLS Logo
            $X = $pdf->GetX();
            $Y = $pdf->GetY();
            $pdf->Image(str_replace($URL, $DURL, $_COMPLIANCE['provider']['logo']), $X, $Y, $_COMPLIANCE['provider']['logo_width'], 0);
            $pdf->Ln(6);
        }

        if (!empty($_COMPLIANCE['details']['show_agent']) || !empty($_COMPLIANCE['details']['show_office'])) {
            if (!empty($_COMPLIANCE['details']['show_agent'])) {
                $agentoffice = $listing['ListingAgent'];
            }
            if (!empty($_COMPLIANCE['details']['show_office'])) {
                $agentoffice = !empty($agentoffice) ? $agentoffice . ', ' . $listing['ListingOffice'] : $listing['ListingOffice'];
            }
            if (!empty($agentoffice)) {
                $pdf->MultiCell(90, 6, (!empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Listing Courtesy of ') . html_entity_decode($agentoffice, ENT_COMPAT | ENT_HTML5, 'ISO-8859-1'));
                if (!empty($_COMPLIANCE['details']['show_office_phone'])) {
                    $pdf->MultiCell(154, 6, 'Office Phone #: ' . $listing['ListingOfficePhoneNumber']);
                }
            }
        }
        if (!empty($_COMPLIANCE['details']['lang']['provider_bold'])) {
            $pdf->SetFont('Arial');
        }
    }

    $pdf->Ln(6);

    // Year Built
    if (!empty($listing['YearBuilt'])) {
        $pdf->Write(1, "Built in {$listing['YearBuilt']}");
        $pdf->Ln(6);
    }

    // Listing Details
    $_DETAILS = $idx->getDetails() ? $idx->getDetails() : array();

    // MLS Compliance (Agent / Office)

    if (!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false) {
        if (is_callable($_COMPLIANCE['details']['extra'])) {
            if ($details_extra = $_COMPLIANCE['details']['extra']($idx, $db_idx, $listing, $_COMPLIANCE)) {
                foreach ($details_extra as $extra) {
                    if (!empty($_COMPLIANCE['details']['details_provider_first'])) {
                        array_unshift($_DETAILS, $extra);
                    } else {
                        array_push($_DETAILS, $extra);
                    }
                }
            }
        } else if (empty($_COMPLIANCE['details']['show_below_remarks']) && (!empty($_COMPLIANCE['details']['show_agent']) || !empty($_COMPLIANCE['details']['show_office']))) {
            $provider_info = array('heading' => (!empty($_COMPLIANCE['details']['lang']['listing_details']) ? $_COMPLIANCE['details']['lang']['listing_details'] : 'Listing Details'), 'fields' => array(
                !empty($_COMPLIANCE['details']['show_agent']) ? array('title' => 'Listing Agent', 'value' => 'ListingAgent') : null,
                !empty($_COMPLIANCE['details']['show_office']) && empty($_COMPLIANCE['details']['show_office_in_header']) ? array('title' => (isset($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Listing Office'),
                                                                        'value' => 'ListingOffice',
                                                                        'font-size' => !empty($_COMPLIANCE['brochure']['office']['size']) ? $_COMPLIANCE['brochure']['office']['size'] : 12,
                                                                        'align' => (!empty($_COMPLIANCE['brochure']['align_office']) ? $_COMPLIANCE['brochure']['align_office'] : null))
                                                                : null,
                !empty($_COMPLIANCE['details']['show_office_phone']) ? array('title' => (isset($_COMPLIANCE['details']['lang']['provider_phone']) ? $_COMPLIANCE['details']['lang']['provider_phone'] : 'Listing Office Phone'), 'value' => 'ListingOfficePhoneNumber') : null,
            ));
            if (!empty($_COMPLIANCE['details']['details_provider_first'])) {
                array_unshift($_DETAILS, $provider_info);
            } else {
                array_push($_DETAILS, $provider_info);
            }
        }
    }

    if ($_DETAILS) :
        foreach ($_DETAILS as $details) :
            // Data Collection
            $data = array();

            // Loop through fields
            foreach ($details['fields'] as $index => $field) {
                if (!empty($field['block'])) {
                    $value = $field['block'];
                    $details['fields'][$index]['value'] = $field['value'] = $value;
                } else {
                    // Field Value
                    $value = $listing[$field['value']];
                }

                // Make sure value is available
                if (empty($value)) {
                    continue;
                }

                // Add to Collection
                $data[$field['value']] = $value;

                // Format if Needed
                if (isset($field['format'])) {
                    $data[$field['value']] = tpl_format($data[$field['value']], $field['format']);
                }

                // Unset Empty Values
                if (empty($data[$field['value']])) {
                    unset($data[$field['value']]);
                }
            }

            // Skip Data Group if there is no Data
            if (empty($data)) {
                continue;
            }

            // Set data column widths
            $w = array(40,150);

            // Set detail group heading
            $pdf->Ln(6);
            $pdf->SetFont('Helvetica', 'B', 13);
            $pdf->Write(1, "{$details['heading']}");
            $pdf->Ln(6);

            // Output Data
            foreach ($details['fields'] as $field) :
                if (!isset($data[$field['value']])) {
                    continue;
                }

                if (isset($field['align']) && !empty($field['align'])) {
                    $pdf->SetFont('Arial', '', !empty($_COMPLIANCE['brochure']['disclaimer_size']) ? $_COMPLIANCE['brochure']['disclaimer_size']: 10);
                    $pdf->MultiCell(0, 3, html_entity_decode($field['title'], ENT_COMPAT | ENT_HTML5, 'ISO-8859-1') . ' ' . html_entity_decode($data[$field['value']], ENT_COMPAT | ENT_HTML5, 'ISO-8859-1'), 0, $field['align']);
                } else {
                    // Set data key
                    $pdf->SetFont('Arial', '', $field['font-size']);
                    $field['title'] = html_entity_decode($field['title'], ENT_COMPAT | ENT_HTML5, 'ISO-8859-1'); /* Convert Entities to Characters */

                    // Adjust the column width for field title in excess of 18 characters
                    if (strlen($field['title']) > 18) {
                        $w[0] = 60;
                    }

                    // Adjust the column width for field title in excess of 36 characters
                    if (strlen($field['title']) > 36) {
                        $w[0] = 90;
                    }

                    $pdf->Cell($w[0], 6, $field['title']);

                    // Set data value
                    $pdf->MultiCell($w[1], 6, $data[$field['value']]);
                    $pdf->Ln(1);
                }
            endforeach;
        endforeach;
    endif;

    $pdf->SetFont('Arial', '', 10);

    if (!empty($_COMPLIANCE['brochure']['office_logo'])) {
        $Y = $pdf->GetY();
        $pdf->Image(str_replace($URL, $DURL, $_COMPLIANCE['brochure']['office_logo']), $pdf->w - 20, $Y - 12, 10, 0);
    }

    // Last Updated Time
    $last_updated = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->lastUpdated($db_idx, $idx);

    // Crea Compliance Logos
    if (!empty($_COMPLIANCE['brochure']['logos']) && is_array($_COMPLIANCE['brochure']['logos'])) {
        foreach ($_COMPLIANCE['brochure']['logos'] as $key => $logo) {
            $Y = $pdf->GetY(); $pdf->Image($logo, 12, $Y, null, 12); $pdf->Ln(12);
        }
    }

    /**
     * Compliance Disclaimer
     */
    if (is_callable($_COMPLIANCE['disclaimer'])) {
        $_COMPLIANCE['disclaimer'] = $_COMPLIANCE['disclaimer']($listing);
    }

    if (!empty($_COMPLIANCE['disclaimer'])) {
        // Disclaimer font size
        if ($_COMPLIANCE['brochure']['disclaimer_size']) {
            $pdf->SetFont('Arial', '', $_COMPLIANCE['brochure']['disclaimer_size']);
        }

        // Find Anchors in Disclaimer
        $links = array();
        foreach ($_COMPLIANCE['disclaimer'] as $l => $line) {
            preg_match_all('!(<a[\s]+(href=[\'"]([^\'"]+)[\'"])([-\w\.\/\s]*)>([-\w\.\/\s]*)<\/a>)!', $line, $matches);
            if (!empty($matches[0])) {
                foreach ($matches[0] as $k => $match) {
                    $text = $matches[5][$k];
                    $href = $matches[3][$k];
                    $href = strpos($href, '/') === 0 ? Settings::getInstance()->URLS['URL'] . ltrim($href, '/') : $href;
                    $links[] = array('href' => $href, 'text' => $text);
                    //unset($_COMPLIANCE['disclaimer'][$l]);
                }
            }
        }

        // Text height
        $height = !empty($_COMPLIANCE['brochure']['disclaimer_height']) ? $_COMPLIANCE['brochure']['disclaimer_height'] : 5;

        if (!empty($_COMPLIANCE['logo'])) {
            $pdf->Ln(5);

            // Multiple logo support. Import legacy settings
            if (!is_array($_COMPLIANCE['logo'])) {
                $_COMPLIANCE['logo'] = array(
                        array(
                            'logo' => $_COMPLIANCE['logo'],
                            'width' => $_COMPLIANCE['logo_width'],
                            'location' => $_COMPLIANCE['logo_location'],
                            'shift_paragraphs' => $_COMPLIANCE['shift_paragraphs']
                        )
                );
            }

            $count = 1;
            foreach ($_COMPLIANCE['disclaimer'] as $disclaimer) {
                // Prep Disclaimer
                ob_start();
                eval(' ?>' . $disclaimer . '<?php ');
                $disclaimer = ob_get_clean();
                $disclaimer = strip_tags($disclaimer);
                $disclaimer_width = 0;

                // Skip Empty Values
                $disclaimer_trim = trim($disclaimer);
                if (empty($disclaimer_trim)) {
                    continue;
                }

                // Place MLS Logo
                foreach ($_COMPLIANCE['logo'] as $logo) {
                    if ($count == $logo['location']) {
                        $X = $pdf->GetX();
                        $Y = $pdf->GetY();

                        if ($logo['align'] == 'right') {
                            $pdf->Image(str_replace($URL, $DURL, $logo['logo']), $pdf->w - $logo['width'] - 10, $Y, $logo['width'], 0);
                            $pdf->SetX($X);
                            $disclaimer_width = $pdf->w - $logo['width'] - 10 - 32;
                        } else if ($logo['align'] == 'center') {
                            $pdf->Image(str_replace($URL, $DURL, $logo['logo']), $pdf->w / 2 - $logo['width'] / 2, $Y, $logo['width'], 0);
                        } else {
                            $pdf->Image(str_replace($URL, $DURL, $logo['logo']), $X, $Y, $logo['width'], 0);
                            // Move File Pointer to Right of Image
                            $pdf->SetX($X + $logo['width'] + 5);
                        }
                    } else if (is_array($logo['shift_paragraphs']) && in_array($count, $logo['shift_paragraphs'])) {
                        // Move File Pointer to Right of Image
                        $pdf->SetX($X + $logo['width'] + 5);
                    }
                }

                $pdf->MultiCell($disclaimer_width, $height, html_entity_decode($disclaimer, ENT_COMPAT | ENT_HTML5, 'ISO-8859-1'));
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
            $pdf->MultiCell(0, $height, html_entity_decode($_COMPLIANCE['disclaimer'], ENT_COMPAT | ENT_HTML5, 'ISO-8859-1'));
        }
    }

    // Last Updated Time
    if (!empty($_COMPLIANCE['update_time'])) {
        $pdf->Ln(2);
        $pdf->Write(1, 'Listing information last updated on ' . date('F jS, Y \a\t g:ia T', strtotime($last_updated)));
    }

    // Include Links
    if (!empty($links)) {
        foreach ($links as $link) {
            $pdf->SetTextColor(0, 0, 255);
            $pdf->SetFont('', 'U');
            $pdf->Ln(5);
            $pdf->Write(6, html_entity_decode($link['text'], ENT_QUOTES, 'UTF-8'), $link['href']);
        }
    }

    // Clean Output
    ob_clean();

    // Send PDF
    $pdf->Output();

    // Exit Script
    exit;
} else {
    // 404 Header
    header('HTTP/1.1 404 NOT FOUND');

    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_keyw  = Lang::write('IDX_DETAILS_META_KEYWORDS_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_desc  = Lang::write('IDX_DETAILS_META_DESCRIPTION_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
}
