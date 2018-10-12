<?php

/**
 * Class of functions that can provide statistics on given input data.
 * @author David
 *
 */
class Util_Statistics
{

    /**
     * Calculates the percentage change between an old value and a new value
     * Example usage is on the
     * @param integer $old
     * @param integer $new
     * @throws UnexpectedValueException
     * @return mixed
     *     false for can't calculate change
     *     0 for no change
     *     +- int
     */
    public static function calculateChangePercentage($old, $new)
    {

        try {
            if (empty($old)) {
                $old = 0;
            }
            if (empty($new)) {
                $new = 0;
            }

            // The Values Must Be Numeric
            if (!is_numeric($old) || !is_numeric($new)) {
                throw new UnexpectedValueException();
            }

            // Cast Values As Integers
            $old = (int) $old;
            $new = (int) $new;

            // If Values Are The Same, No Change
            if ($old === $new) {
                return 0;
            }

            // Can't Divide By Zero
            if ($old === 0) {
                return false;
            }

            return (int) ((($new - $old) / $old) * 100);
        } catch (UnexpectedValueException $e) {
            if ($e->getMessage() !== '') {
                throw $e;
            } else {
                throw new UnexpectedValueException("Input values must be numeric");
            }
        }
    }
}
