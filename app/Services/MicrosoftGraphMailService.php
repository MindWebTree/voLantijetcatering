<?php

namespace App\Services;

use Exception;

class MicrosoftGraphMailService
{
    private $tenantId;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->tenantId = config('graph.tenant_id');
        $this->clientId = config('graph.client_id');
        $this->clientSecret = config('graph.client_secret');
    }

    /**
     * Get OAuth2 access token
     */
    public function getAccessToken()
    {
        $tokenEndpoint = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";

        $postData = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => 'https://graph.microsoft.com/.default'
        ];

        $response = \Illuminate\Support\Facades\Http::asForm()->post($tokenEndpoint, $postData);

        if (!$response->successful()) {
            throw new Exception("Token error: " . $response->body());
        }

        return $response->json()['access_token'];
    }

    /**
     * Send email via Microsoft Graph API
     */
    public function sendMail($fromEmail, $toEmail, $subject, $bodyHtml)
    {
        $accessToken = $this->getAccessToken();

        if (!$fromEmail) {
            throw new \Exception("Sender email is NULL — GRAPH_USER_EMAIL not loaded.");
        }

        $endpoint = "https://graph.microsoft.com/v1.0/users/$fromEmail/sendMail";

        $emailData = [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $bodyHtml
                ],
                'toRecipients' => [
                    [
                        'emailAddress' => ['address' => $toEmail]
                    ]
                ]
            ],
            'saveToSentItems' => true
        ];

        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->post($endpoint, $emailData);

        if ($response->status() !== 202) {
            throw new Exception("Mail send error: " . $response->body());
        }

        return true;
    }
}
