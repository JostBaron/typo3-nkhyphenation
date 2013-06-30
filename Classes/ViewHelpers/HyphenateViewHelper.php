<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HyphenationViewHelper
 *
 * @author Jost Baron <j.baron@netzkoenig.de>
 */
class Tx_Nkhyphenation_ViewHelpers_HyphenateViewHelper
    extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    public function render() {

        $hyphenator = new Tx_Nkhyphenation_Utility_Hyphenator('de');

        return $hyphenator->hyphenate($this->renderChildren());
    }
}

?>