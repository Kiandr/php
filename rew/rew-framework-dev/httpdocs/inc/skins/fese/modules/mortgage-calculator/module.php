<?php

    // Canadian Mortgage Calculator (or USA)
    $canadian = Settings::getInstance()->LANG == 'en-CA' ? true : false;

    // Submit Form
if (isset($_GET['calculate'])) {
    // Require Numeric Values
    $sale_price       = preg_replace("/[^0-9.]/i", '', $_GET['sale_price']);
    $interest_percent = preg_replace("/[^0-9.]/i", '', $_GET['interest_percent']);
    $year_term        = preg_replace("/[^0-9.]/i", '', $_GET['year_term']);
    $down_percent     = preg_replace("/[^0-9.]/i", '', $_GET['down_percent']);

    // Check Required
    if (((float) $year_term <= 0) || ((float) $sale_price <= 0) || ((float) $interest_percent <= 0)) {
        $error = 'You must enter a <strong>Sale Price of Home</strong>, <strong>Length of Mortgage</strong> and <strong>Annual Interest Rate</strong>';
    } else {
        // Calculate
        $month_term            = $year_term * 12;
        $down_payment          = $sale_price * ($down_percent / 100);
        $annual_interest_rate  = $interest_percent / 100;
        $financing_price       = $sale_price - $down_payment;

        // Canada
        if (!empty($canadian)) {
            $monthly_interest_rate   = pow(pow((1 + ($annual_interest_rate / 2)), 2), (1 / 12)) - 1;
            $monthly_payment         = ($financing_price * $monthly_interest_rate) / (1 - pow((1 + $monthly_interest_rate), -$month_term));

            // USA
        } else {
            $monthly_interest_rate   = $annual_interest_rate / 12;

            // Calculate interest factor
            $factor      = 0;
            $base_rate   = 1 + $monthly_interest_rate;
            $denominator = $base_rate;
            for ($i=0; $i < ($year_term * 12); $i++) {
                $factor += (1 / $denominator);
                $denominator *= $base_rate;
            }

            $monthly_factor          = $factor;
            $monthly_payment         = $financing_price / $monthly_factor;
        }
    }
} else {
    // Set Defaults
    $_GET['sale_price']       = isset($_GET['sale_price'])       ? $_GET['sale_price']       : 150000; // Sale Price ($)
    $_GET['interest_percent'] = isset($_GET['interest_percent']) ? $_GET['interest_percent'] : 7.0;    // Annual Interest Rate (%)
    $_GET['year_term']        = isset($_GET['year_term'])        ? $_GET['year_term']        : 30;     // Mortgage Length (Years)
    $_GET['down_percent']     = isset($_GET['down_percent'])     ? $_GET['down_percent']     : 10;     // Down Payment (%)
    $_GET['show_progress']    = isset($_GET['show_progress'])    ? $_GET['show_progress']    : true;   // Show Calculations
}

    // Display Error
if (!empty($error)) {
    printf('<div class="msg msg--neg marB-md">%s</div>', $error);
}

?>

<p>
    This <strong>mortgage calculator</strong> can be used to figure out monthly payments of a home mortgage loan, based on the home's sale price, the term of the loan desired, buyer's down payment percentage, and the loan's interest rate.
    <?php if (empty($canadian)) { ?>
        This calculator factors in PMI (Private Mortgage Insurance) for loans where less than 20% is put as a down payment. Also taken into consideration are the town property taxes, and their effect on the total monthly mortgage payment.
    <?php } ?>
</p>

<form class="marB-lg">

    <input type="hidden" name="calculate" value="1">

    <h4>Purchase & Financing Information</h4>

    <div class="cols">

        <div class="fld col w1/2">
            <label>Sale Price of Home (In Dollars)</label>
            <input name="sale_price" value="<?=htmlentities($_GET['sale_price']); ?>">
        </div>

        <div class="fld col w1/2">
            <label>Percentage Down (%)</label>
            <input name="down_percent" value="<?=htmlentities($_GET['down_percent']); ?>">
        </div>

        <div class="fld col w1/2">
            <label>Length of Mortgage (In Years)</label>
            <input name="year_term" value="<?=htmlentities($_GET['year_term']); ?>">
        </div>

        <div class="fld col w1/2">
            <label>Annual Interest Rate (%)</label>
            <input name="interest_percent" value="<?=htmlentities($_GET['interest_percent']); ?>">
        </div>

        <div class="col w1/1">
            <label>
                <input type="checkbox" style="display: inline; width: auto" name="show_progress" value="1"<?=!empty($_GET['show_progress']) ? ' checked' : ''; ?>>
                Show me the calculations and amortization
            </label>
        </div>

    </div>

    <div class="btns marT-sm">
        <button class="btn btn--primary" type="submit">Calculate</button>
        <?=isset($_GET['calculate']) ? '<a href="?" class="btn btn--primary">Start Over</a>' : ''; ?>
    </div>

</form>

<?php if (isset($_GET['calculate']) && $monthly_payment && !$canadian) { ?>

    <h2 class="txtC marV-md">Mortgage Payment Information</h2>

    <div class="kvs">
        <div class="kv">
            <div class="k">Down Payment:</div>
            <div class="v"><strong>$<?=number_format($down_payment, 2); ?></strong></div>
        </div>
        <div class="kv">
            <div class="k">Amount Financed:</div>
            <div class="v"><strong>$<?=number_format($financing_price, 2); ?></strong></div>
        </div>
        <div class="kv">
            <div class="k">Monthly Payment:</div>
            <div class="v">
                <strong>$<?=number_format($monthly_payment, 2); ?></strong>
                (Principal &amp; Interest ONLY)
            </div>
        </div>
    </div>
    <?php if ($down_percent < 20) { ?>
        <?php $pmi_per_month = 55 * ($financing_price / 100000); ?>
        <p class="marV-md">
            Since you are putting LESS than 20% down, you will need to pay PMI (<a href="http://www.google.com/search?hl=en&q=private+mortgage+insurance" target="_blank">Private Mortgage Insurance</a>), which tends to be about $55 per month for every $100,000 financed (until you have paid off 20% of your loan).<br>
            <strong>This could add $<?=number_format($pmi_per_month, 2); ?> to your monthly payment.</strong>
        </p>
        <div class="kvs">
            <div class="kv">
                <div class="k">Insured Monthly Payment:</div>
                <div class="v">
                    <strong>$<?=number_format($monthly_payment + $pmi_per_month, 2); ?></strong>
                    (Principal &amp; Interest, and PMI)
                </div>
            </div>
        </div>
    <?php } ?>
    <?php

        // Tax Details
        $assessed_price          = ($sale_price * .85);
        $residential_yearly_tax  = ($assessed_price / 1000) * 14;
        $residential_monthly_tax = $residential_yearly_tax / 12;
        $pmi_text = ($pmi_per_month) ? 'PMI and ' : '';

    ?>
    <p class="marV-md">
        Residential (or Property) Taxes are a little harder to figure out... the average residential tax rate seems to be around $14 per year for every $1,000 of your property's assessed value.
        Let's say that your property's <i>assessed value</i> is 85% of what you actually paid for it - $<?=number_format($assessed_price, 2); ?>.
        This would mean that your yearly residential taxes will be around $<?=number_format($residential_yearly_tax, 2); ?>.<br>
        <strong>This could add $<?=number_format($residential_monthly_tax, 2); ?> to your monthly payment.</strong>
    </p>
    <div class="kvs marB-md">
        <div class="kv">
            <div class="k">TOTAL Monthly Payment:</div>
            <div class="v">
                <strong>$<?=number_format($monthly_payment + $pmi_per_month + $residential_monthly_tax, 2); ?></strong>
                (including <?=$pmi_text; ?> Residential Tax)
            </div>
        </div>
    </div>

<?php } ?>

<?php if (isset($_GET['calculate']) && !empty($_GET['show_progress'])) {
    $step = 1; ?>

    <h2 class="txtC marV-md">Calculations and Amortization</h2>

    <div class="kvs">
        <div class="kv">
            <div class="k"><strong><?= $step++; ?></strong></div>
            <div class="v txtL">
                The <strong>down payment</strong> = The price of the home multiplied by the percentage down divided by
                100 (for 5% down becomes 5/100 or 0.05)<br>
                $<?= number_format($down_payment, 2); ?> = $<?= number_format($sale_price, 2); ?> X
                (<?= $down_percent; ?> / 100)
            </div>
        </div>
        <div class="kv">
            <div class="k"><strong><?= $step++; ?></strong></div>
            <div class="v txtL">
                The <strong>interest rate</strong> = The annual interest percentage divided by 100<br>
                <?= $annual_interest_rate; ?> = <?= $interest_percent; ?>% / 100
            </div>
        </div>
        <div class="kv">
            <div class="v txtL">
                The <strong>monthly factor</strong> = The result of the following formula:
            </div>
        </div>
        <div class="kv">
            <div class="k"><strong><?= $step++; ?></strong></div>
            <div class="v txtL">
                The <strong>monthly interest rate</strong> = The annual interest rate divided by 12 (for the 12 months
                in a year)<br>
                <?= $monthly_interest_rate; ?> = <?= $annual_interest_rate; ?> / 12
            </div>
        </div>
        <div class="kv">
            <div class="k"><strong><?= $step++; ?></strong></div>
            <div class="v txtL">
                The <strong>month term</strong> of the loan in months = The number of years you've taken the loan out
                for times 12<br>
                <?= $month_term; ?> Months = <?= $year_term; ?> Years X 12
            </div>
        </div>
        <div class="kv">
            <div class="k"><strong><?= $step++; ?></strong></div>
            <div class="v txtL">
                The monthly payment is figured out using the following formula:<br>
                Monthly Payment = <?= number_format($financing_price, 2); ?> *
                (<?= number_format($monthly_interest_rate, 4); ?> / (1 - ((1
                + <?= number_format($monthly_interest_rate, 4); ?>) - <?= $month_term; ?>)))
                <br><br>
                The <a href="#amortization">amortization</a> breaks down how much of your monthly payment goes towards
                the bank's interest, and how much goes into paying off the principal of your loan.
            </div>
        </div>
    </div>

    <?php

    // Set some base variables
    $current_month = 1;
    $current_year = 1;

    // Canada
    if (!empty($canadian)) {
        $remaining_balance = $financing_price;

        // USA
    } else {
        $principal = $financing_price;
        $power = -($month_term);
        $denom = pow((1 + $monthly_interest_rate), $power);
        $monthly_payment = $principal * ($monthly_interest_rate / (1 - $denom));
    }

    // Monthly amortization
    echo '<a name="amortization"></a>';
    printf(
        '<p class="msg">Amortization For Monthly Payment: <strong>$%s over %s years</strong></p>',
        number_format($monthly_payment, 2),
        $year_term
    );

    // This LEGEND will get reprinted every 12 months
    $legend = '<div class="cols">';
    $legend .= '<div class="col w1/4 hid-sm"><strong>Month</strong></div>';
    $legend .= '<div class="col w1/4 w1/3-sm"><strong>Interest <span class="hid-sm">Paid</span></strong></div>';
    $legend .= '<div class="col w1/4 w1/3-sm"><strong>Principal <span class="hid-sm">Paid</strong></div>';
    $legend .= '<div class="col w1/4 w1/3-sm"><strong><span class="hid-sm">Remaining</span> Balance</strong></div>';
    $legend .= '</div>';
    echo $legend;

    // Loop through and get the current month's payments for
    // the length of the loan
    while ($current_month <= $month_term) {
        // Canada
        if (!empty($canadian)) {
            $interest_paid = $monthly_interest_rate * $remaining_balance;
            $principal_paid = $monthly_payment - $interest_paid;
            $remaining_balance = $remaining_balance - $principal_paid;

        // USA
        } else {
            $interest_paid = $principal * $monthly_interest_rate;
            $principal_paid = $monthly_payment - $interest_paid;
            $remaining_balance = $principal - $principal_paid;
        }

        $this_year_interest_paid = $this_year_interest_paid + $interest_paid;
        $this_year_principal_paid = $this_year_principal_paid + $principal_paid;

        echo '<div class="cols mc-payment">';
        printf('<div class="col w1/4 hid-sm">%s</div>', $current_month);
        printf('<div class="col w1/4 w1/3-sm">$%s</div>', number_format($interest_paid, 2));
        printf('<div class="col w1/4 w1/3-sm">$%s</div>', number_format($principal_paid, 2));
        printf('<div class="col w1/4 w1/3-sm">$%s</div>', number_format($remaining_balance, 2));
        echo '</div>';

        ($current_month % 12) ? $show_legend = false : $show_legend = true;

        if ($show_legend) {
            $total_spent_this_year = $this_year_interest_paid + $this_year_principal_paid;

            echo '<div class="kvs marV-md">';
            printf('<strong>Year %d TOTAL:</strong> $%s', $current_year, number_format($total_spent_this_year, 2));
            printf('<br><strong>INTEREST:</strong> $%s', number_format($this_year_interest_paid, 2));
            printf('<br><strong>PRINCIPAL:</strong> $%s', number_format($this_year_principal_paid, 2));
            echo '</div>';

            $current_year++;
            $this_year_interest_paid = 0;
            $this_year_principal_paid = 0;

            if (($current_month + 6) < $month_term) {
                echo $legend;
            }
        }

        // USA Only
        if (empty($canadian)) {
            $principal = $remaining_balance;
        }

        $current_month++;
    }
}