<?php

declare(strict_types=1);

namespace Ksfraser\AsteriskPBX\Service;

use Ksfraser\AsteriskPBX\Entity\AsteriskCall;
use Ksfraser\AsteriskPBX\Integration\AsteriskAMI;

class PhoneNumberMatcher
{
    public function normalizePhoneNumber(string $phone): string
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    public function extractAreaCode(string $phone): ?string
    {
        $normalized = $this->normalizePhoneNumber($phone);
        
        if (preg_match('/^\+?1?(\d{3})/', $normalized, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    public function formatForDisplay(string $phone, string $format = 'US'): string
    {
        $normalized = preg_replace('/[^0-9]/', '', $this->normalizePhoneNumber($phone));
        
        if (strlen($normalized) === 10) {
            return "({$normalized[0]}{$normalized[1]}{$normalized[2]}) {$normalized[3]}{$normalized[4]}{$normalized[5]}-{$normalized[6]}{$normalized[7]}{$normalized[8]}{$normalized[9]}";
        }
        
        if (strlen($normalized) === 11 && $normalized[0] === '1') {
            return "+1 ({$normalized[1]}{$normalized[2]}{$normalized[3]}) {$normalized[4]}{$normalized[5]}{$normalized[6]}-{$normalized[7]}{$normalized[8]}{$normalized[9]}{$normalized[10]}";
        }
        
        return $phone;
    }

    public function isMobile(string $phone): bool
    {
        $normalized = preg_replace('/[^0-9]/', '', $this->normalizePhoneNumber($phone));
        
        $mobilePrefixes = ['1604', '1609', '1614', '1615', '1616', '1617', '1618', '1620', '1622', '1623', '1624'];
        $areaCode = substr($normalized, -10, 3);
        
        return in_array($areaCode, $mobilePrefixes);
    }
}

class CallPopupService
{
    private AsteriskAMI $ami;
    private PhoneNumberMatcher $phoneMatcher;

    public function __construct(AsteriskAMI $ami)
    {
        $this->ami = $ami;
        $this->phoneMatcher = new PhoneNumberMatcher();
    }

    public function handleNewCall(array $event): ?AsteriskCall
    {
        $call = new AsteriskCall();
        $call->setUniqueId($event['UniqueID'] ?? uniqid('call_'));
        $call->setCallerNumber($event['CallerIDNum'] ?? $event['From'] ?? '');
        $call->setCalledNumber($event['DestNum'] ?? $event['To'] ?? '');
        $call->setChannel($event['Channel'] ?? '');
        $call->setDirection($this->determineDirection($event));
        $call->setCallStartTime(date('Y-m-d H:i:s'));
        $call->setUniqueId($event['UniqueID'] ?? '');

        $status = $event['Event'] ?? 'Newchannel';
        if ($status === 'Hangup') {
            $call->hangup();
        } elseif ($status === 'Bridge') {
            $call->answer();
        }

        return $call;
    }

    public function findMatchingContact(string $phoneNumber): ?array
    {
        $normalized = $this->phoneMatcher->normalizePhoneNumber($phoneNumber);
        
        return [
            'type' => 'contact',
            'id' => null,
            'name' => 'Unknown',
            'account_name' => '',
            'phone_mobile' => $normalized,
            'email' => '',
        ];
    }

    public function showPopup(AsteriskCall $call, ?array $contact = null): array
    {
        return [
            'call_id' => $call->getId(),
            'unique_id' => $call->getUniqueId(),
            'caller_number' => $call->getCallerNumber(),
            'caller_display' => $this->phoneMatcher->formatForDisplay($call->getCallerNumber()),
            'called_number' => $call->getCalledNumber(),
            'direction' => $call->getDirection(),
            'status' => $call->getStatus(),
            'contact' => $contact,
            'is_mobile' => $this->phoneMatcher->isMobile($call->getCallerNumber()),
            'popup_type' => $contact ? 'contact' : 'unknown',
            'actions' => [
                'answer' => true,
                'dial' => true,
                'sms' => $this->phoneMatcher->isMobile($call->getCallerNumber()),
                'voicemail' => true,
                'log' => true,
            ],
        ];
    }

    public function initiateClickToCall(int $userId, string $fromNumber, string $toNumber, ?int $contactId = null): ClickToCallRequest
    {
        $request = new ClickToCallRequest();
        $request->setUserId($userId);
        $request->setFromNumber($fromNumber);
        $request->setToNumber($toNumber);
        $request->setContactId($contactId);
        $request->initiate();

        $channel = "Local/{$fromNumber}@from-internal";
        $callId = $this->ami->originateCall($channel, $toNumber);

        if ($callId) {
            $request->complete($callId);
        } else {
            $request->fail();
        }

        return $request;
    }

    private function determineDirection(array $event): string
    {
        $channel = $event['Channel'] ?? '';
        
        if (strpos($channel, 'Local/') === 0) {
            return AsteriskCall::DIRECTION_INTERNAL;
        }
        
        if (strpos($channel, 'IAX2/') === 0 || strpos($channel, 'SIP/') === 0) {
            $direction = $event['Direction'] ?? 'incoming';
            return strpos($direction, 'outgoing') !== false ? AsteriskCall::DIRECTION_OUTBOUND : AsteriskCall::DIRECTION_INBOUND;
        }
        
        return AsteriskCall::DIRECTION_INBOUND;
    }
}

class SMSService
{
    private AsteriskAMI $ami;
    private PhoneNumberMatcher $phoneMatcher;

    public function __construct(AsteriskAMI $ami)
    {
        $this->ami = $ami;
        $this->phoneMatcher = new PhoneNumberMatcher();
    }

    public function sendSMS(string $from, string $to, string $message): ?SMSMessage
    {
        $sms = new SMSMessage();
        $sms->setFromNumber($from);
        $sms->setToNumber($to);
        $sms->setMessage($message);
        $sms->setDirection(SMSMessage::DIRECTION_OUTBOUND);
        $sms->setSentAt(date('Y-m-d H:i:s'));

        $externalId = $this->ami->sendSMS($from, $to, $message);
        
        if ($externalId) {
            $sms->setExternalId($externalId);
            $sms->setStatus('sent');
        } else {
            $sms->setStatus('failed');
        }

        return $sms;
    }

    public function handleIncomingSMS(array $event): SMSMessage
    {
        $sms = new SMSMessage();
        $sms->setFromNumber($event['Source'] ?? '');
        $sms->setToNumber($event['Destination'] ?? '');
        $sms->setMessage($event['Body'] ?? '');
        $sms->setDirection(SMSMessage::DIRECTION_INBOUND);
        $sms->setSentAt(date('Y-m-d H:i:s'));

        return $sms;
    }

    public function linkToContact(SMSMessage $sms, int $contactId): void
    {
        $sms->setContactId($contactId);
    }

    public function linkToLead(SMSMessage $sms, int $leadId): void
    {
        $sms->setLeadId($leadId);
    }

    public function linkToAccount(SMSMessage $sms, int $accountId): void
    {
        $sms->setAccountId($accountId);
    }
}