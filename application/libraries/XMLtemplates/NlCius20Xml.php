<?php

require_once 'AbstractXmlTemplate.php';

class NlCius20Xml extends AbstractXmlTemplate
{
    protected function xmlRoot()
    {
        $node = parent::xmlRoot();
        $node->setAttribute('CustomizationID', 'Dutch CIUS 1.0');

        return $node;
    }
}
