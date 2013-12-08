<?php

namespace GooglePosta\Command;

use Command\Abstraction\CommandInterface;
use Config\Config;
use GooglePosta\Entity\ClientData;

class InitializeApiBridge implements CommandInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ClientData
     */
    private $clientData;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @param Config $config
     */
    function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param \GooglePosta\Entity\ClientData $clientData
     *
     * @return InitializeApiBridge
     */
    public function setClientData($clientData)
    {
        $this->clientData = $clientData;

        return $this;
    }

    /**
     * @return \GooglePosta\Entity\ClientData
     */
    public function getClientData()
    {
        return $this->clientData;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Execute the command
     *
     * @return CommandInterface
     */
    public function execute()
    {
        $client = new \Google_Client();
        $client->setClientId($this->config->get('google.client_id'));
        $client->setClientSecret($this->config->get('google.client_secret'));
        $client->setRedirectUri($this->config->get('google.return_url'));
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');

        $client->setScopes(
            array(
                'https://www.google.com/m8/feeds',
            )
        );

        $this->redirectUrl = $client->createAuthUrl();
    }
}
