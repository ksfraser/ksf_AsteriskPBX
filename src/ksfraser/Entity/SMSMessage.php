<?php

declare(strict_types=1);

namespace Ksfraser\Entity;

class SMSMessage
{
    public const DIRECTION_INBOUND = 'inbound';
    public const DIRECTION_OUTBOUND = 'outbound';

    private ?int $id = null;
    private string $fromNumber = '';
    private string $toNumber = '';
    private string $direction = self::DIRECTION_INBOUND;
    private string $message = '';
    private string $status = 'sent';
    private ?string $sentAt = null;
    private ?string $deliveredAt = null;
    private ?int $contactId = null;
    private ?int $leadId = null;
    private ?int $accountId = null;
    private ?string $externalId = null;
    private string $provider = 'asterisk';

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function getFromNumber(): string { return $this->fromNumber; }
    public function setFromNumber(string $fromNumber): self { $this->fromNumber = $fromNumber; return $this; }
    public function getToNumber(): string { return $this->toNumber; }
    public function setToNumber(string $toNumber): self { $this->toNumber = $toNumber; return $this; }
    public function getDirection(): string { return $this->direction; }
    public function setDirection(string $direction): self { $this->direction = $direction; return $this; }
    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): self { $this->message = $message; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getSentAt(): ?string { return $this->sentAt; }
    public function setSentAt(?string $sentAt): self { $this->sentAt = $sentAt; return $this; }
    public function getDeliveredAt(): ?string { return $this->deliveredAt; }
    public function setDeliveredAt(?string $deliveredAt): self { $this->deliveredAt = $deliveredAt; return $this; }
    public function getContactId(): ?int { return $this->contactId; }
    public function setContactId(?int $contactId): self { $this->contactId = $contactId; return $this; }
    public function getLeadId(): ?int { return $this->leadId; }
    public function setLeadId(?int $leadId): self { $this->leadId = $leadId; return $this; }
    public function getAccountId(): ?int { return $this->accountId; }
    public function setAccountId(?int $accountId): self { $this->accountId = $accountId; return $this; }
    public function getExternalId(): ?string { return $this->externalId; }
    public function setExternalId(?string $externalId): self { $this->externalId = $externalId; return $this; }
    public function getProvider(): string { return $this->provider; }
    public function setProvider(string $provider): self { $this->provider = $provider; return $this; }

    public function isInbound(): bool { return $this->direction === self::DIRECTION_INBOUND; }
    public function isDelivered(): bool { return $this->deliveredAt !== null; }
}

class ClickToCallRequest
{
    private ?int $id = null;
    private int $userId = 0;
    private string $fromNumber = '';
    private string $toNumber = '';
    private ?int $contactId = null;
    private string $status = 'pending';
    private ?string $callId = null;
    private ?string $createdAt = null;
    private string $notes = '';

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): self { $this->userId = $userId; return $this; }
    public function getFromNumber(): string { return $this->fromNumber; }
    public function setFromNumber(string $fromNumber): self { $this->fromNumber = $fromNumber; return $this; }
    public function getToNumber(): string { return $this->toNumber; }
    public function setToNumber(string $toNumber): self { $this->toNumber = $toNumber; return $this; }
    public function getContactId(): ?int { return $this->contactId; }
    public function setContactId(?int $contactId): self { $this->contactId = $contactId; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getCallId(): ?string { return $this->callId; }
    public function setCallId(?string $callId): self { $this->callId = $callId; return $this; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function getNotes(): string { return $this->notes; }
    public function setNotes(string $notes): self { $this->notes = $notes; return $this; }

    public function initiate(): void { $this->status = 'initiated'; $this->createdAt = date('Y-m-d H:i:s'); }
    public function complete(string $callId): void { $this->status = 'completed'; $this->callId = $callId; }
    public function fail(): void { $this->status = 'failed'; }
}