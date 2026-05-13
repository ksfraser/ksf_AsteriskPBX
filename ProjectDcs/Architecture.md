# ksf_AsteriskPBX - Architecture

## Document Information
- **Module**: ksf_AsteriskPBX (Asterisk PBX Integration)
- **Version**: 1.0.0
- **Date**: 2026-05-13
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Architecture Overview

### 1.1 Design Principles
The ksf_AsteriskPBX module follows these architectural principles:

1. **Service Abstraction**: Asterisk operations encapsulated in service classes
2. **Event-Driven**: AMI events trigger business logic handlers
3. **Connection Management**: Centralized AMI connection handling
4. **Phone Number Intelligence**: Comprehensive phone number processing

### 1.2 Technology Stack
- **PHP**: 8.0+ with strict typing
- **Protocol**: Asterisk Manager Interface (AMI) over TCP
- **Architecture**: Service-Oriented with Entity models
- **Integration**: Event-based communication

---

## 2. Directory Structure

```
ksf_AsteriskPBX/
├── composer.json
├── src/
│   └── Ksfraser/
│       └── AsteriskPBX/              # Namespace for module
│           ├── Entity/
│           │   ├── AsteriskCall.php  # Call entity model
│           │   └── SMSMessage.php    # SMS and ClickToCall models
│           ├── Integration/
│           │   └── AsteriskAMI.php   # AMI client wrapper
│           └── Service/
│               └── CallServices.php  # Call handling services
├── tests/
│   └── Unit/
│       └── AsteriskCallTest.php
└── ProjectDcs/
    ├── Business Requirements.md
    ├── Architecture.md
    ├── Functional Requirements.md
    ├── Use Case.md
    ├── Test Plan.md
    └── UAT Plan.md
```

---

## 3. Class Architecture

### 3.1 Entity Layer

#### AsteriskCall
```php
namespace Ksfraser\AsteriskPBX\Entity;

class AsteriskCall
{
    // Direction constants
    const DIRECTION_INBOUND = 'inbound';
    const DIRECTION_OUTBOUND = 'outbound';
    const DIRECTION_INTERNAL = 'internal';
    
    // Status constants
    const STATUS_RINGING = 'ringing';
    const STATUS_ANSWERED = 'answered';
    const STATUS_HUNG_UP = 'hung_up';
    const STATUS_MISSED = 'missed';
    const STATUS_VOICEMAIL = 'voicemail';
    
    // Link types
    const LINKED_NONE = 'none';
    const LINKED_CONTACT = 'contact';
    const LINKED_LEAD = 'lead';
    const LINKED_ACCOUNT = 'account';
    
    // Properties
    private ?int $id;
    private string $callerNumber;
    private string $calledNumber;
    private string $direction;
    private string $status;
    private ?string $callStartTime;
    private ?string $callEndTime;
    private ?int $duration;
    private ?int $linkedContactId;
    private string $linkedType;
    private ?int $linkedAccountId;
    private ?int $userId;
    private string $uniqueId;
    private string $channel;
    private ?string $recordingUrl;
    private string $notes;
    
    // Methods
    public function getId(): ?int;
    public function setCallerNumber(string $number): self;
    public function answer(): void;
    public function hangup(): void;
    public function miss(): void;
    public function linkToContact(int $contactId): void;
    public function isInbound(): bool;
    public function isLinked(): bool;
    // ... additional getters/setters
}
```

#### SMSMessage
```php
namespace Ksfraser\AsteriskPBX\Entity;

class SMSMessage
{
    const DIRECTION_INBOUND = 'inbound';
    const DIRECTION_OUTBOUND = 'outbound';
    
    private ?int $id;
    private string $fromNumber;
    private string $toNumber;
    private string $direction;
    private string $message;
    private string $status;
    private ?string $sentAt;
    private ?string $deliveredAt;
    private ?int $contactId;
    private ?int $leadId;
    private ?int $accountId;
    private ?string $externalId;
    private string $provider;
    
    // Methods
    public function isInbound(): bool;
    public function isDelivered(): bool;
    // ... additional getters/setters
}

class ClickToCallRequest
{
    private ?int $id;
    private int $userId;
    private string $fromNumber;
    private string $toNumber;
    private ?int $contactId;
    private string $status;
    private ?string $callId;
    private ?string $createdAt;
    private string $notes;
    
    // Methods
    public function initiate(): void;
    public function complete(string $callId): void;
    public function fail(): void;
}
```

### 3.2 Integration Layer

#### AsteriskAMI
```php
namespace Ksfraser\AsteriskPBX\Integration;

class AsteriskAMI
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private $socket;
    private bool $connected;
    
    // Connection management
    public function connect(): bool;
    public function disconnect(): void;
    public function isConnected(): bool;
    
    // Call operations
    public function originateCall(string $channel, string $extension, ...): ?string;
    public function hangup(string $channel): bool;
    public function transferCall(string $channel, string $extension, ...): bool;
    public function getChannelStatus(string $channel): ?string;
    
    // Recording
    public function recordCall(string $channel, string $filename, int $duration = 0): bool;
    public function stopRecording(string $channel): bool;
    
    // DTMF
    public function sendDTMF(string $channel, string $digits): bool;
    
    // SMS
    public function sendSMS(string $from, string $to, string $message): ?string;
    
    // Queue management
    public function queueAdd(string $queue, string $channel, int $priority = 0): bool;
    public function queueRemove(string $queue, string $channel): bool;
    public function getQueueStatus(string $queue): ?array;
    
    // Private helpers
    private function sendCommand(string $command): void;
    private function readResponse(): string;
}
```

### 3.3 Service Layer

#### PhoneNumberMatcher
```php
class PhoneNumberMatcher
{
    public function normalizePhoneNumber(string $phone): string;
    public function extractAreaCode(string $phone): ?string;
    public function formatForDisplay(string $phone, string $format = 'US'): string;
    public function isMobile(string $phone): bool;
}
```

#### CallPopupService
```php
class CallPopupService
{
    private AsteriskAMI $ami;
    private PhoneNumberMatcher $phoneMatcher;
    
    public function handleNewCall(array $event): ?AsteriskCall;
    public function findMatchingContact(string $phoneNumber): ?array;
    public function showPopup(AsteriskCall $call, ?array $contact = null): array;
    public function initiateClickToCall(int $userId, string $fromNumber, string $toNumber, ?int $contactId = null): ClickToCallRequest;
}
```

#### SMSService
```php
class SMSService
{
    private AsteriskAMI $ami;
    private PhoneNumberMatcher $phoneMatcher;
    
    public function sendSMS(string $from, string $to, string $message): ?SMSMessage;
    public function handleIncomingSMS(array $event): SMSMessage;
    public function linkToContact(SMSMessage $sms, int $contactId): void;
    public function linkToLead(SMSMessage $sms, int $leadId): void;
    public function linkToAccount(SMSMessage $sms, int $accountId): void;
}
```

---

## 4. Data Flow Diagrams

### 4.1 Inbound Call Flow
```
[Asterisk PBX]
     |
     | AMI Event (Newchannel, Dial, Bridge)
     v
[AsteriskAMI Listener]
     |
     | Parse event
     v
[CallPopupService]
     |
     +---> [PhoneNumberMatcher] ---> Match contact
     |
     +---> [AsteriskCall Entity] ---> Create call record
     |
     v
[CRM Contact Lookup]
     |
     v
[Display Popup] ---> Show caller info + actions
```

### 4.2 Click-to-Call Flow
```
[User clicks phone number]
     |
     v
[CallPopupService]
     |
     +---> [ClickToCallRequest Entity]
     |
     v
[AsteriskAMI.originateCall()]
     |
     v
[Asterisk PBX] ---> Connect call
     |
     v
[Update ClickToCallRequest status]
     |
     v
[Log call in CRM]
```

### 4.3 SMS Flow
```
[User sends SMS] OR [Incoming SMS received]
     |
     v
[SMSService]
     |
     +---> [AsteriskAMI.sendSMS()] for outbound
     |
     +---> Parse AMI SMS event for inbound
     |
     v
[SMSMessage Entity]
     |
     v
[Link to Contact/Lead/Account]
     |
     v
[Store in database]
```

---

## 5. Database Schema (Future)

### 5.1 Proposed Tables

#### asterisk_calls
```sql
CREATE TABLE fa_asterisk_calls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unique_id VARCHAR(100) NOT NULL UNIQUE,
    caller_number VARCHAR(50) NOT NULL,
    called_number VARCHAR(50) NOT NULL,
    direction VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL,
    call_start_time DATETIME,
    call_end_time DATETIME,
    duration INT,
    channel VARCHAR(100),
    linked_type VARCHAR(20),
    linked_contact_id INT,
    linked_lead_id INT,
    linked_account_id INT,
    user_id INT,
    recording_url VARCHAR(500),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_unique_id (unique_id),
    INDEX idx_caller (caller_number),
    INDEX idx_called (called_number),
    INDEX idx_status (status)
);
```

#### asterisk_sms
```sql
CREATE TABLE fa_asterisk_sms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_number VARCHAR(50) NOT NULL,
    to_number VARCHAR(50) NOT NULL,
    direction VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(20) NOT NULL,
    external_id VARCHAR(100),
    sent_at DATETIME,
    delivered_at DATETIME,
    contact_id INT,
    lead_id INT,
    account_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_from (from_number),
    INDEX idx_to (to_number)
);
```

---

## 6. API Design

### 6.1 AsteriskAMI Interface
```php
interface AsteriskAMIInterface
{
    public function connect(): bool;
    public function disconnect(): void;
    public function isConnected(): bool;
    public function originateCall(string $channel, string $extension, string $context = 'default', int $priority = 1): ?string;
    public function hangup(string $channel): bool;
    public function transferCall(string $channel, string $extension, string $context = 'default'): bool;
    public function getChannelStatus(string $channel): ?string;
    public function recordCall(string $channel, string $filename, int $duration = 0): bool;
    public function stopRecording(string $channel): bool;
    public function sendDTMF(string $channel, string $digits): bool;
    public function sendSMS(string $from, string $to, string $message): ?string;
    public function queueAdd(string $queue, string $channel, int $priority = 0): bool;
    public function queueRemove(string $queue, string $channel): bool;
    public function getQueueStatus(string $queue): ?array;
}
```

### 6.2 Service Factory
```php
class AsteriskServiceFactory
{
    public static function createAMI(string $host, int $port, string $user, string $pass): AsteriskAMI;
    public static function createCallPopupService(AsteriskAMI $ami): CallPopupService;
    public static function createSMSService(AsteriskAMI $ami): SMSService;
}
```

---

## 7. Error Handling

### 7.1 Exception Hierarchy
```php
class AsteriskException extends \RuntimeException
class AsteriskConnectionException extends AsteriskException
class AsteriskAuthenticationException extends AsteriskException
class AsteriskCommandException extends AsteriskException
class AsteriskPhoneNumberException extends AsteriskException
```

### 7.2 Error Recovery
- **Connection Loss**: Automatic reconnection with exponential backoff
- **Command Timeout**: 5-second timeout with retry capability
- **Authentication Failure**: Log error, alert admin, disable auto-reconnect

---

## 8. Security Considerations

### 8.1 AMI Credentials
- Store credentials in secure configuration
- Use environment variables or encrypted config
- Rotate credentials regularly

### 8.2 Input Validation
- Validate all phone numbers before use
- Sanitize AMI commands
- Escape data in SQL queries (when DB added)

### 8.3 Access Control
- Restrict click-to-call to authorized users
- Log all call attempts for audit

---

## 9. Performance Considerations

### 9.1 Connection Pooling
- Single AMI connection per application instance
- Keep-alive heartbeat every 30 seconds
- Connection health monitoring

### 9.2 Caching
- Cache contact phone lookups
- Invalidate cache on contact update
- TTL: 5 minutes for contact data

### 9.3 Async Operations
- AMI commands are synchronous
- Use message queue for bulk operations (future)
- Non-blocking UI updates

---

## 10. Extension Points

### 10.1 Custom Event Handlers
```php
// Register custom AMI event handler
$ami->onEvent('UserEvent', function(array $event) {
    // Custom processing
});
```

### 10.2 Contact Matcher Plugin
```php
interface ContactMatcherInterface
{
    public function match(string $phoneNumber): ?Contact;
}

// Register custom matcher
$callPopup->setContactMatcher(new CustomMatcher());
```

### 10.3 Notification Hooks
```php
// Hook into call events
add_hook('asterisk.call.started', function($call) {
    // Send notification
});
```

---

## 11. Testing Architecture

### 11.1 Unit Tests
- AsteriskCall entity tests
- SMSMessage entity tests
- PhoneNumberMatcher tests
- Field validation tests

### 11.2 Integration Tests (Future)
- AMI connection tests
- Call originate tests
- SMS send/receive tests
- Queue operation tests

### 11.3 Mock AMI Server
- Use `asterisk-mock` for testing
- Simulate AMI responses
- Test error conditions

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-13*