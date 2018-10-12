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
    printf('<div class="notice notice--negative -mar-bottom">%s</div>', $error);
}

?>

<p>
    This <strong>mortgage calculator</strong> can be used to figure out monthly payments of a home mortgage loan, based on the home's sale price, the term of the loan desired, buyer's down payment percentage, and the loan's interest rate.
    <?php if (empty($canadian)) { ?>
        This calculator factors in PMI (Private Mortgage Insurance) for loans where less than 20% is put as a down payment. Also taken into consideration are the town property taxes, and their effect on the total monthly mortgage payment.
    <?php } ?>
</p>


<div class="-theme-muted -pad -pad-lg@lg -mar-vertical-lg -rounded">

    <form class="-mar-bottom-lg">

        <input type="hidden" name="calculate" value="1">

        <h2>Purchase &amp; Financing Information</h2>

        <div class="columns">

            <div class="field column -width-1/2 -width-1/1@xs -width-1/1@sm">
                <label class="field__label">Sale Price of Home <small>(In Dollars)</small></label>
                <input name="sale_price" value="<?=htmlentities($_GET['sale_price']); ?>">
            </div>

            <div class="field column -width-1/2 -width-1/1@xs -width-1/1@sm">
                <label class="field__label">Percentage Down <small>(%)</small></label>
                <input name="down_percent" value="<?=htmlentities($_GET['down_percent']); ?>">
            </div>

            <div class="field column -width-1/2 -width-1/1@xs -width-1/1@sm">
                <label class="field__label">Length of Mortgage <small>(In Years)</small></label>
                <input name="year_term" value="<?=htmlentities($_GET['year_term']); ?>">
            </div>

            <div class="field column -width-1/2 -width-1/1@xs -width-1/1@sm">
                <label class="field__label">Annual Interest Rate <small>(%)</small></label>
                <input name="interest_percent" value="<?=htmlentities($_GET['interest_percent']); ?>">
            </div>

            <div class="field column -width-1/1">
                <label class="field__label">
                    <input type="checkbox" name="show_progress" value="1"<?=!empty($_GET['show_progress']) ? ' checked' : ''; ?>>
                    Show me the calculations and amortization
                </label>
            </div>

        </div>

        <div class="buttons -mar-top-sm -clear mc-buttons">
            <button class="button button--pill button--strong -mar-right-sm -mar-bottom-sm@sm -mar-bottom-sm@xs" type="submit">Calculate</button>
            <?=isset($_GET['calculate']) ? '<a href="?" class="button button--sm button--bordered">Start Over</a>' : ''; ?>
        </div>

    </form>

</div>

<?php if (isset($_GET['calculate']) && $monthly_payment && !$canadian) { ?>
<div class="mc-payment">
    <h2 class="-mar-vertical-md">Mortgage Payment Information</h2>

    <div class="keyvals">
        <div class="columns">
            <div class="column -width-1/4 -width-1/3@sm -width-1/2@xs">Down Payment:</div>
            <div class="column -width-3/4 -width-2/3@sm -width-1/2@xs"><strong>$<?=number_format($down_payment, 2); ?></strong></div>
        </div>
        <div class="columns">
            <div class="column -width-1/4 -width-1/3@sm -width-1/2@xs">Amount Financed:</div>
            <div class="column -width-3/4 -width-2/3@sm -width-1/2@xs"><strong>$<?=number_format($financing_price, 2); ?></strong></div>
        </div>
        <div class="columns">
            <div class="column -width-1/4 -width-1/3@sm -width-1/2@xs">Monthly Payment:</div>
            <div class="column -width-3/4 -width-2/3@sm -width-1/2@xs">
                <strong>$<?=number_format($monthly_payment, 2); ?></strong>
                (Principal &amp; Interest ONLY)
            </div>
        </div>
    </div>
    <?php if ($down_percent < 20) { ?>
        <?php $pmi_per_month = 55 * ($financing_price / 100000); ?>
        <p class="-mar-vertical-md">
            Since you are putting LESS than 20% down, you will need to pay PMI (<a href="http://www.google.com/search?hl=en&q=private+mortgage+insurance" target="_blank">Private Mortgage Insurance</a>), which tends to be about $55 per month for every $100,000 financed (until you have paid off 20% of your loan).<br>
            <strong>This could add $<?=number_format($pmi_per_month, 2); ?> to your monthly payment.</strong>
        </p>
        <div class="columns">
            <div class="column -width-1/4 -width-1/3@sm -width-1/2@xs">Insured Monthly Payment:</div>
            <div class="column -width-3/4 -width-2/3@sm -width-1/2@xs">
                <strong>$<?=number_format($monthly_payment + $pmi_per_month, 2); ?></strong>
                (Principal &amp; Interest, and PMI)
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
    <p class="-mar-vertical-md">
        Residential (or Property) Taxes are a little harder to figure out... the average residential tax rate seems to be around $14 per year for every $1,000 of your property's assessed value.
        Let's say that your property's <i>assessed value</i> is 85% of what you actually paid for it - $<?=number_format($assessed_price, 2); ?>.
        This would mean that your yearly residential taxes will be around $<?=number_format($residential_yearly_tax, 2); ?>.<br>
        <strong>This could add $<?=number_format($residential_monthly_tax, 2); ?> to your monthly payment.</strong>
    </p>
    <div class="-mar-bottom-md">
        <div class="columns">
            <div class="column -width-1/4 -width-1/3@sm -width-1/1@xs">TOTAL Monthly Payment:</div>
            <div class="column -width-3/4 -width-2/3@sm -width-1/1@xs">
                <strong>$<?=number_format($monthly_payment + $pmi_per_month + $residential_monthly_tax, 2); ?></strong>
                (including <?=$pmi_text; ?> Residential Tax)
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if (isset($_GET['calculate']) && !empty($_GET['show_progress'])) {
    $step = 1; ?>
<div class="mc-payment">
    <h2 class="txtC -mar-vertical-md">Calculations and Amortization</h2>

    <div class="columns">
        <div class="column -width-1/5"><strong><?= $step++; ?></strong></div>
        <div class="column -width-4/5">
            The <strong>down payment</strong> = The price of the home multiplied by the percentage down divided by
            100 (for 5% down becomes 5/100 or 0.05)<br>
            $<?= number_format($down_payment, 2); ?> = $<?= number_format($sale_price, 2); ?> X
            (<?= $down_percent; ?> / 100)
        </div>
    </div>
    <div class="columns">
        <div class="column -width-1/5"><strong><?= $step++; ?></strong></div>
        <div class="column -width-4/5">
            The <strong>interest rate</strong> = The annual interest percentage divided by 100<br>
            <?= $annual_interest_rate; ?> = <?= $interest_percent; ?>% / 100
        </div>
    </div>
    <div class="columns">
        <div class="column -width-1/1 txtL">
            The <strong>monthly factor</strong> = The result of the following formula:
        </div>
    </div>
    <div class="columns">
        <div class="column -width-1/5"><strong><?= $step++; ?></strong></div>
        <div class="column -width-4/5">
            The <strong>monthly interest rate</strong> = The annual interest rate divided by 12 (for the 12 months
            in a year)<br>
            <?= $monthly_interest_rate; ?> = <?= $annual_interest_rate; ?> / 12
        </div>
    </div>
    <div class="columns">
        <div class="column -width-1/5"><strong><?= $step++; ?></strong></div>
        <div class="column -width-4/5">
            The <strong>month term</strong> of the loan in months = The number of years you've taken the loan out
            for times 12<br>
            <?= $month_term; ?> Months = <?= $year_term; ?> Years X 12
        </div>
    </div>
    <div class="columns">
        <div class="column -width-1/5"><strong><?= $step++; ?></strong></div>
        <div class="column -width-4/5">
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
        '<p class="highlight">Amortization For Monthly Payment: <strong>$%s over %s years</strong></p>',
        number_format($monthly_payment, 2),
        $year_term
    );

    // This LEGEND will get reprinted every 12 months
    $legend = '<div class="columns">';
    $legend .= '<div class="column -width-1/4 -is-hidden@sm -is-hidden@xs"><strong>Month</strong></div>';
    $legend .= '<div class="column -width-1/4 -width-1/3@sm -width-1/3@xs"><strong>Interest <span class="-is-hidden@sm -is-hidden@xs">Paid</span></strong></div>';
    $legend .= '<div class="column -width-1/4 -width-1/3@sm -width-1/3@xs"><strong>Principal <span class="-is-hidden@sm -is-hidden@xs">Paid</strong></div>';
    $legend .= '<div class="column -width-1/4 -width-1/3@sm -width-1/3@xs"><strong><span class="-is-hidden@sm -is-hidden@xs">Remaining</span> Balance</strong></div>';
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

        echo '<div class="columns mc-payment">';
        printf('<div class="column -width-1/4 -is-hidden@sm -is-hidden@xs">%s</div>', $current_month);
        printf('<div class="column -width-1/4 -width-1/3@sm -width-1/3@xs">$%s</div>', number_format($interest_paid, 2));
        printf('<div class="column -width-1/4 -width-1/3@sm -width-1/3@xs">$%s</div>', number_format($principal_paid, 2));
        printf('<div class="column -width-1/4 -width-1/3@sm -width-1/3@xs">$%s</div>', number_format($remaining_balance, 2));
        echo '</div>';

        ($current_month % 12) ? $show_legend = false : $show_legend = true;

        if ($show_legend) {
            $total_spent_this_year = $this_year_interest_paid + $this_year_principal_paid;

            echo '<div class="keyvals -mar-vertical-md">';
            echo '<div class="keyvals__body -well">';
            echo '<div class="keyval">';
            printf('<strong class="key">Year %d TOTAL:</strong> <span class="val">$%s</span>', $current_year, number_format($total_spent_this_year, 2));
            echo '</div>';
            echo '<div class="keyval">';
            printf('<strong class="key">INTEREST:</strong> <span class="val">$%s</span>', number_format($this_year_interest_paid, 2));
            echo '</div>';
            echo '<div class="keyval">';
            printf('<strong class="key">PRINCIPAL:</strong> <span class="val">$%s</span>', number_format($this_year_principal_paid, 2));
            echo '</div>';
            echo '</div>';
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