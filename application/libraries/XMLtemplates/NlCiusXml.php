<?php

class NlCiusXml extends AbstractXmlTemplate
{
    protected function xmlRoot()
    {
        $node = parent::xmlRoot();
        $node->setAttribute('CustomizationID', 'Dutch CIUS 1.0');
        return $node;
    }
}
