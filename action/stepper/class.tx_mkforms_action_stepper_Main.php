<?php

/**
 * Plugin 'act_stepper' for the 'ameos_formidable' extension.
 *
 * @author    Jerome Schneider <typo3dev@ameos.com>
 */
class tx_mkforms_action_stepper_Main extends formidable_mainactionlet
{
    public function _doTheMagic($aRendered, $sForm)
    {
        $sUrl = null;

        if ($this->oForm->oDataHandler->_allIsValid()) {
            $iStep = $this->oForm->_getStep();

            switch ($this->aElement['step']) {
                case 'next':

                    $iStepToGo = $this->oForm->_getNextInArray(
                        $iStep,
                        $this->oForm->aSteps,
                        false,    // cycle ?
                        true    // key only ?
                    );

                    break;

                case 'previous':

                    $iStepToGo = $this->oForm->_getPrevInArray(
                        $iStep,
                        $this->oForm->aSteps,
                        false,
                        true
                    );

                    break;

                default:
                    $iStepToGo = $iStep;
                }

            $sUid = '';

            if (array_key_exists('uid', $this->aElement)) {
                switch ($this->aElement['uid']) {
                        case 'follow':
                            $sUid = $this->oForm->oDataHandler->_currentEntryId();
                            break;

                        default:
                            $sUid = $this->aElement['uid'];
                        }
            }

            $sStepperId = $this->oForm->_getStepperId();

            tx_mkforms_session_Factory::getSessionManager()->initialize();

            if (!array_key_exists('ameos_formidable', $GLOBALS['_SESSION'])) {
                $GLOBALS['_SESSION']['ameos_formidable'] = [];
            }

            if (!array_key_exists('stepper', $GLOBALS['_SESSION']['ameos_formidable'])) {
                $GLOBALS['_SESSION']['ameos_formidable']['stepper'] = [];
            }

            $GLOBALS['_SESSION']['ameos_formidable']['stepper'][$sStepperId] = [
                'AMEOSFORMIDABLE_STEP' => $iStepToGo,
                'AMEOSFORMIDABLE_STEP_UID' => $sUid,
                'AMEOSFORMIDABLE_STEP_HASH' => $this->oForm->_getSafeLock($iStepToGo.$sUid),
            ];

            $sUrl = Tx_Rnbase_Utility_T3General::getIndpEnv('TYPO3_REQUEST_URL');

            if (!is_null($sUrl)) {
                header('Location: '.$sUrl);
                die();
            }
        }
    }
}

if (defined('TYPO3_MODE')
    && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkforms/action/stepper/class.tx_mkforms_action_stepper_Main.php']
) {
    include_once $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkforms/action/stepper/class.tx_mkforms_action_stepper_Main.php'];
}
