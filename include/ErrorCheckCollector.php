<?php
/**
 * Error check collector allows to collect error tests (so-called checks) invarious
 * various groups and later evaluate them and write HTML code.
 *
 * @author rugk
 * @copyright Copyright (c) 2016 rugk
 * @license MIT
 */

class ErrorChkColl
{
    const ERR_NONE    = 0;
    const ERR_WARNING = 1;
    const ERR_ERROR   = 2;

    const CHAR_MULTIPLE_MSG = ' ';

    const ERR_NONE_PIC    = 'assets/img/tick.svg';
    const ERR_WARNING_PIC = 'assets/img/warning.svg';
    const ERR_ERROR_PIC   = 'assets/img/cross.svg';

    /**
     * Saves all the infromation about the error checks.
     *
     * format:
     *  - a group ($groupIdentifier)
     *      - subgroup called check ($checkIdentifier)
     *          - errors contains a list of error checks, may be multiple ones
     *            if the check uses multiple checks and can return more than
     *            one error.
     *
     * @var array
     */
    private $checkedErrors = [];

    public function registerGenericMessages($groupIdentifier, $checkIdentifier, $successMsg, $errorMsg = '')
    {
        $this->checkedErrors[$groupIdentifier][$checkIdentifier]['successMsg'] = $successMsg;
        $this->checkedErrors[$groupIdentifier][$checkIdentifier]['errorMsg']   = $errorMsg;
    }

    public function registerResult($groupIdentifier, $checkIdentifier, $result, $errorType, $errorMsg)
    {
        if ($result) {
            // if there is no error force error state to be ERR_NONE
            $errorType = self::ERR_NONE;
        }

        $this->checkedErrors[$groupIdentifier][$checkIdentifier]['errors'][] = [
            'errorType' => $errorType,
            'errorMsg' => $errorMsg
        ];
    }

    public function registerSuccess($groupIdentifier, $checkIdentifier, $errorMsg = '')
    {
        return $this->registerResult($groupIdentifier, $checkIdentifier, true, self::ERR_NONE, $errorMsg);
    }

    public function registerWarning($groupIdentifier, $checkIdentifier, $errorMsg = '')
    {
        return $this->registerResult($groupIdentifier, $checkIdentifier, false, self::ERR_WARNING, $errorMsg);
    }

    public function registerError($groupIdentifier, $checkIdentifier, $errorMsg = '')
    {
        return $this->registerResult($groupIdentifier, $checkIdentifier, false, self::ERR_ERROR, $errorMsg);
    }

    /**
     * Returns an array with all checks and errors of a specific group.
     *
     * @param  string $groupIdentifier The unique string of a group of checks.
     * @param  bool   $autoSuccess     Automatically set unknown checks to successful,
     *                                 if disabled these checks are ignored.
     * @return array
     */
    public function evaluateChecks($groupIdentifier, $autoSuccess = true)
    {
        $resultArray     = [];
        $groupWorstError = 0;

        if ($autoSuccess) {
            //check whether there are some identifier without an result
            foreach ($this->checkedErrors[$groupIdentifier] as $checkIdentifier => $checkContent) {
                //automatically register success if there is no error registered
                if (!array_key_exists('errors', $checkContent)) {
                    $this->registerSuccess($groupIdentifier, $checkIdentifier);
                }
            }
        }

        //go through every $checkIdentifier
        foreach ($this->checkedErrors[$groupIdentifier] as $checkIdentifier => $checkContent) {
            $successMsgCollect = '';
            $errorMsgCollect   = '';
            $errorTypeCollect  = 0;

            //ignore missing error keys
            if (!array_key_exists('errors', $checkContent)) {
                continue;
            }

            // go through every checked error
            foreach ($checkContent['errors'] as $errorCheck) {
                if ($errorCheck['errorType'] == self::ERR_NONE) {
                    //collect information about the success
                    if ($successMsgCollect != '') {
                        $successMsgCollect .= self::HAR_MULTIPLE_ERR;
                    }
                    $successMsgCollect .= $errorCheck['errorMsg'];
                } else {
                    //yes there is an error
                    if ($groupWorstError < $errorCheck['errorType']) {
                        //if the error is worse (heigher number) than all ones before use new number
                        $groupWorstError = $errorCheck['errorType'];
                    }

                    //collect information about this error
                    if ($errorMsgCollect != '') {
                        $errorMsgCollect .= self::CHAR_MULTIPLE_MSG;
                    }
                    $errorMsgCollect .= $errorCheck['errorMsg'];

                    if ($errorTypeCollect < $errorCheck['errorType']) {
                        //if the error is worse (heigher number) than all ones before use new number
                        $errorTypeCollect = $errorCheck['errorType'];
                    }
                }
            }

            //check whether there is an error in the whole identifier
            if ($errorTypeCollect == 0) {
                //no error
                $resultArray[$checkIdentifier]['error']          = self::ERR_NONE;
                $resultArray[$checkIdentifier]['genericMessage'] = $this->valueOrEmpty($checkContent, 'successMsg');
                $resultArray[$checkIdentifier]['message']        = $successMsgCollect;
            } else {
                //there is an error
                $resultArray[$checkIdentifier]['error']          = $errorTypeCollect;
                $resultArray[$checkIdentifier]['genericMessage'] = $this->valueOrEmpty($checkContent, 'errorMsg');
                $resultArray[$checkIdentifier]['message']        = $errorMsgCollect;
            }
        }

        //evaluate whole goup result
        $resultArray['error'] = $groupWorstError;
        return $resultArray;
    }

    /**
     * Returns an array with all checks and errors of a specific group.
     *
     * @param  array $evaluatedArray An evaluate array returned by evaluateChecks.
     * @return void
     */
    public function showErrors($evaluatedArray, $showSuccess = true)
    {
        // go through every error and write it
        foreach ($evaluatedArray as $check) {
            if (!is_array($check)) {
                //skip non-array content
                continue;
            }
            if ($check['error'] || $showSuccess) {
                $this->errorWrite($check['error'], $check['genericMessage'], $check['message']);
            }
        }
    }

    /**
     * Returns true if there is a stopping error in the evaluation.
     *
     * @param  array $evaluatedArray An evaluate array returned by evaluateChecks.
     * @return bool
     */
    public function isBreakpoint($evaluatedArray)
    {
        return ($evaluatedArray['error'] >= self::ERR_ERROR);
    }

    private function errorWrite($errorType, $genericMsg, $message)
    {
        //Combine messages
        $fullMsg = $genericMsg;
        if ($message != '') {
            $fullMsg .= ' ' . $message;
        }
        //Do not write output if there is no message content
        if ($fullMsg == '') {
            return;
        }

        echo '<div class="check">';
        switch ($errorType) {
            case self::ERR_NONE:
                echo '<img class="graphicon" src="' . self::ERR_NONE_PIC . '" alt="tick" />' . PHP_EOL;
                echo '<span class="checkdescr">' . $fullMsg . '</span>' . PHP_EOL;
                break;
            case self::ERR_WARNING:
                echo '<img class="graphicon" src="' . self::ERR_WARNING_PIC . '" alt="warning" />' . PHP_EOL;
                echo '<span class="checkdescr">' . $fullMsg . '</span>' . PHP_EOL;
                break;
            case self::ERR_ERROR:
                echo '<img class="graphicon" src="' . self::ERR_ERROR_PIC . '" alt="error" />' . PHP_EOL;
                echo '<span class="checkdescr">' . $fullMsg . '</span>' . PHP_EOL;
                break;
        echo '</div>';
        }
    }

    private function valueOrEmpty($array, $key)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return '';
        }
    }
}
