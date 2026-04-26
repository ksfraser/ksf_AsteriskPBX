<?php

declare(strict_types=1);

namespace Ksfraser\AsteriskPBX\Entity;

class AsteriskCall
{
    public const DIRECTION_INBOUND = 'inbound';
    public const DIRECTION_OUTBOUND = 'outbound';
    public const DIRECTION_INTERNAL = 'internal';

    public const STATUS_RINGING = 'ringing';
    public const STATUS_ANSWERED = 'answered';
    public const STATUS_HUNG_UP = 'hung_up';
    public const STATUS_MISSED = 'missed';
    public const STATUS_VOICEMAIL = 'voicemail';

    public const LINKED_NONE = 'none';
    public const LINKED_CONTACT = 'contact';
    public const LINKED_LEAD = 'lead';
    public const LINKED_ACCOUNT = 'account';

    private ?int $id = null;
    private string $callerNumber = '';
    private string $calledNumber = '';
    private string $direction = self::DIRECTION_INBOUND;
    private string $status = self::STATUS_RINGING;
    private ?string $callStartTime = null;
    private ?string $callEndTime = null;
    private ?int $duration = null;
    private ?int $linkedContactId = null;
    private string $linkedType = self::LINKED_NONE;
    private ?int $linkedAccountId = null;
    private ?int $userId = null;
    private string $uniqueId = '';
    private string $channel = '';
    private ?string $recordingUrl = null;
    private string $notes = '';

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function getCallerNumber(): string { return $this->callerNumber; }
    public function setCallerNumber(string $callerNumber): self { $this->callerNumber = $callerNumber; return $this; }
    public function getCalledNumber(): string { return $this->calledNumber; }
    public function setCalledNumber(string $calledNumber): self { $this->calledNumber = $calledNumber; return $this; }
    public function getDirection(): string { return $this->direction; }
    public function setDirection(string $direction): self { $this->direction = $direction; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getCallStartTime(): ?string { return $this->callStartTime; }
    public function setCallStartTime(?string $callStartTime): self { $this->callStartTime = $callStartTime; return $this; }
    public function getCallEndTime(): ?string { return $this->callEndTime; }
    public function setCallEndTime(?string $callEndTime): self { $this->callEndTime = $callEndTime; return $this; }
    public function getDuration(): ?int { return $this->duration; }
    public function setDuration(?int $duration): self { $this->duration = $duration; return $this; }
    public function getLinkedContactId(): ?int { return $this->linkedContactId; }
    public function setLinkedContactId(?int $linkedContactId): self { $this->linkedContactId = $linkedContactId; return $this; }
    public function getLinkedType(): string { return $this->linkedType; }
    public function setLinkedType(string $linkedType): self { $this->linkedType = $linkedType; return $this; }
    public function getLinkedAccountId(): ?int { return $this->linkedAccountId; }
    public function setLinkedAccountId(?int $linkedAccountId): self { $this->linkedAccountId = $linkedAccountId; return $this; }
    public function getUserId(): ?int { return $this->userId; }
    public function setUserId(?int $userId): self { $this->userId = $userId; return $this; }
    public function getUniqueId(): string { return $this->uniqueId; }
    public function setUniqueId(string $uniqueId): self { $this->uniqueId = $uniqueId; return $this; }
    public function getChannel(): string { return $this->channel; }
    public function setChannel(string $channel): self { $this->channel = $channel; return $this; }
    public function getRecordingUrl(): ?string { return $this->recordingUrl; }
    public function setRecordingUrl(?string $recordingUrl): self { $this->recordingUrl = $recordingUrl; return $this; }
    public function getNotes(): string { return $this->notes; }
    public function setNotes(string $notes): self { $this->notes = $notes; return $this; }

    public function isInbound(): bool { return $this->direction === self::DIRECTION_INBOUND; }
    public function isOutbound(): bool { return $this->direction === self::DIRECTION_OUTBOUND; }
    public function isMissed(): bool { return $this->status === self::STATUS_MISSED; }
    public function isAnswered(): bool { return $this->status === self::STATUS_ANSWERED; }
    public function isLinked(): bool { return $this->linkedType !== self::LINKED_NONE && $this->linkedContactId !== null; }

    public function answer(): void { $this->status = self::STATUS_ANSWERED; }
    public function hangup(): void { $this->status = self::STATUS_HUNG_UP; $this->callEndTime = date('Y-m-d H:i:s'); }
    public function miss(): void { $this->status = self::STATUS_MISSED; }

    public function linkToContact(int $contactId): void
    {
        $this->linkedContactId = $contactId;
        $this->linkedType = self::LINKED_CONTACT;
    }

    public function linkToLead(int $leadId): void
    {
        $this->linkedContactId = $leadId;
        $this->linkedType = self::LINKED_LEAD;
    }

    public function linkToAccount(int $accountId): void
    {
        $this->linkedAccountId = $accountId;
        $this->linkedType = self::LINKED_ACCOUNT;
    }
}