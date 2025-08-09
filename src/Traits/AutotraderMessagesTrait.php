<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Traits;

use Illuminate\Support\Facades\Validator;
use NorthBees\AutotraderApi\Enum\AutotraderEndpoints;
use NorthBees\AutotraderApi\Enum\HttpMethods;
use NorthBees\AutotraderApi\Exceptions\AutotraderException;

trait AutotraderMessagesTrait
{
    /**
     * Get messages for a specific message ID.
     *
     * @param int $advertiserId The advertiser ID
     * @param string $messagesId The messages ID to retrieve
     * @return array The messages response
     * @throws AutotraderException
     */
    public function getMessages(int $advertiserId, string $messagesId): array
    {
        $url = AutotraderEndpoints::Messages->value . '/' . $messagesId . '?advertiserId=' . $advertiserId;

        return $this->performRequest(HttpMethods::GET, $url, [], []);
    }

    /**
     * Mark messages as read by updating the advertiser last read status.
     *
     * @param int $advertiserId The advertiser ID
     * @param string $messagesId The messages ID to mark as read
     * @return array The response
     * @throws AutotraderException
     */
    public function markMessagesAsRead(int $advertiserId, string $messagesId): array
    {
        $url = AutotraderEndpoints::Messages->value . '/' . $messagesId . '?advertiserId=' . $advertiserId;
        
        $data = [
            'advertiserLastReadStatus' => 'Read'
        ];

        return $this->performRequest(HttpMethods::PATCH, $url, [], $data);
    }

    /**
     * Send a new message.
     *
     * @param int $advertiserId The advertiser ID
     * @param array $messageData The message data (must contain either 'dealId' for new conversation or 'messagesId' for existing, plus 'message')
     * @return array The response
     * @throws AutotraderException
     */
    public function sendMessage(int $advertiserId, array $messageData): array
    {
        $validator = Validator::make($messageData, [
            'message' => 'required|string|max:1500',
            'dealId' => 'required_without:messagesId|string',
            'messagesId' => 'required_without:dealId|string',
        ]);

        if ($validator->fails()) {
            throw new AutotraderException((string) $validator->errors());
        }

        $url = AutotraderEndpoints::Messages->value . '?advertiserId=' . $advertiserId;

        return $this->performRequest(HttpMethods::POST, $url, [], $messageData);
    }
}