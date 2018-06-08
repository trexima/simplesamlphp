<?php

namespace SimpleSAML\Module\saml\Auth\Binding;

use DOMDocument;

use SAML2\Binding;
use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use SAML2\Message;
use SAML2\Response;
use SAML2\Utils;
use SAML2\XML\ecp\Response as ECPResponse;

/**
 * Class which implements the PAOS binding.
 *
 * @package SimpleSAMLphp
 */
class PAOS extends Binding
{
    public function getOutputToSend(Message $message, $envelope = null)
    {
        if (!isset($envelope)) {

            $envelope = <<<SOAP
<?xml version="1.0" encoding="utf-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="%s">
    <SOAP-ENV:Header />
    <SOAP-ENV:Body />
</SOAP-ENV:Envelope>
SOAP;

            $envelope = sprintf($envelope, Constants::NS_SOAP);
        }

        $doc = new DOMDocument;
        $doc->loadXML($envelope);

        $body = $doc->getElementsByTagNameNs(Constants::NS_SOAP, 'Body')->item(0);

        $body->appendChild($doc->importNode($message->toSignedXML(), true));

        return $doc->saveXML();
    }

    /**
     * Send a SAML 2 message using the SOAP binding.
     *
     * Note: This function never returns.
     *
     * @param \SAML2\Message $message The message we should send.
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function send(Message $message, $envelope = null)
    {
        header('Content-Type: application/vnd.paos+xml', true);

        $xml = $this->getOutputToSend($message, $envelope);
        Utils::getContainer()->debugMessage($xml, 'out');
        echo $xml;

        exit(0);
    }

    protected function getInputStream()
    {
        return file_get_contents('php://input');
    }

    /**
     * Receive a SAML 2 message.
     *
     * This function will extract the message from the current request.
     * An exception will be thrown if we are unable to process the message.
     *
     * @return \SAML2\Message The received message.
     */
    public function receive()
    {
        // TODO: Implement receive() method.
    }
}
