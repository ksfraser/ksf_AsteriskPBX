# ksf_AsteriskPBX - Functional Requirements

## Document Information
- **Module**: ksf_AsteriskPBX (Asterisk PBX Integration)
- **Version**: 1.0.0
- **Date**: 2026-05-13
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Overview

### 1.1 Purpose
This document defines the functional requirements for the ksf_AsteriskPBX module, which provides integration between Asterisk IP PBX systems and the KSFII platform.

### 1.2 Scope
The module provides:
- Asterisk Manager Interface (AMI) connectivity
- Call event handling and call management
- Click-to-call functionality
- SMS messaging integration
- Phone number intelligence
- Contact/Lead/Account linking

---

## 2. Asterisk AMI Connectivity

### 2.1 Connection Management (FR-AMI-001)
**Requirement**: The system shall establish and maintain AMI connections.

**Features**:
- Connect to Asterisk AMI via TCP socket
- Authenticate with username/password
- Handle connection failures gracefully
- Auto-reconnect on connection loss
- Heartbeat/keepalive mechanism

**Priority**: Critical

### 2.2 Command Execution (FR-AMI-002)
**Requirement**: The system shall send AMI commands and parse responses.

**Features**:
- Send arbitrary AMI commands
- Parse response format (key: value)
- Handle multi-line responses
- Timeout handling (5 seconds)
- Error detection

**Priority**: Critical

---

## 3. Call Management

### 3.1 Call Origination (FR-CALL-001)
**Requirement**: The system shall originate outbound calls via AMI.

**Parameters**:
- `channel` - SIP channel (e.g., "Local/1000@from-internal")
- `extension` - Destination number
- `context` - Dialplan context (default: "default")
- `priority` - Extension priority (default: 1)

**Returns**: Unique call ID on success, null on failure

**Priority**: Critical

### 3.2 Call Control (FR-CALL-002)
**Requirement**: The system shall control active calls.

**Features**:
- Hangup call by channel name
- Transfer call to another extension
- Get channel status
- Check if channel exists

**Priority**: High

### 3.3 Call Recording (FR-CALL-003)
**Requirement**: The system shall manage call recordings.

**Features**:
- Start MixMonitor recording
- Stop recording
- Specify recording filename
- Set recording duration
- Store recording URL reference

**Priority**: Medium

### 3.4 DTMF Signaling (FR-CALL-004)
**Requirement**: The system shall send DTMF tones during calls.

**Features**:
- Send single DTMF digit
- Send DTMF sequence
- Use PlayDTMF AMI action
- Support all DTMF digits (0-9, *, #, A-D)

**Priority**: Low

---

## 4. Click-to-Call

### 4.1 Initiate Click-to-Call (FR-CTC-001)
**Requirement**: The system shall initiate outbound calls on user request.

**Input**:
- `userId` - Initiating user ID
- `fromNumber` - Source phone number/extension
- `toNumber` - Destination phone number
- `contactId` - Optional linked contact ID

**Process**:
1. Validate phone numbers
2. Create ClickToCallRequest record
3. Execute AMI Originate command
4. Update request status on result

**Output**: ClickToCallRequest entity with status

**Priority**: High

### 4.2 Click-to-Call Status (FR-CTC-002)
**Requirement**: The system shall track click-to-call request status.

**Statuses**:
- `pending` - Request created
- `initiated` - Call started
- `completed` - Call connected
- `failed` - Call failed

**Priority**: High

---

## 5. Call Popup

### 5.1 Call Event Handling (FR-POP-001)
**Requirement**: The system shall process incoming AMI call events.

**Events**:
- `Newchannel` - New channel created
- `Dial` - Call dialing
- `Bridge` - Calls connected
- `Hangup` - Call ended

**Priority**: High

### 5.2 Contact Matching (FR-POP-002)
**Requirement**: The system shall match caller to contacts by phone number.

**Matching Logic**:
1. Normalize phone number (remove formatting)
2. Query contacts by phone numbers
3. Check mobile, phone, other phones
4. Return best match with score

**Priority**: High

### 5.3 Popup Display Data (FR-POP-003)
**Requirement**: The system shall prepare popup display data.

**Data Fields**:
- `caller_number` - Calling number
- `caller_display` - Formatted display number
- `called_number` - Called number
- `direction` - inbound/outbound
- `status` - Current call status
- `contact` - Matched contact data
- `is_mobile` - Boolean
- `actions` - Available action buttons

**Priority**: High

---

## 6. SMS Messaging

### 6.1 Send SMS (FR-SMS-001)
**Requirement**: The system shall send SMS messages via Asterisk.

**Input**:
- `from` - Source number
- `to` - Destination mobile number
- `message` - SMS content

**Process**:
1. Validate numbers (detect mobile)
2. Create SMSMessage entity
3. Execute AMI SMS command
4. Update status on response

**Output**: SMSMessage entity with status

**Priority**: Medium

### 6.2 Receive SMS (FR-SMS-002)
**Requirement**: The system shall handle incoming SMS events.

**Process**:
1. Parse AMI SMS event
2. Create SMSMessage entity
3. Link to contact if matched
4. Store message

**Priority**: Medium

### 6.3 SMS Linking (FR-SMS-003)
**Requirement**: The system shall link SMS to entities.

**Link Types**:
- Contact (CRM)
- Lead (CRM)
- Account (CRM)

**Priority**: Medium

---

## 7. Phone Number Intelligence

### 7.1 Normalization (FR-PHONE-001)
**Requirement**: The system shall normalize phone numbers.

**Operations**:
- Remove all non-numeric characters except +
- Preserve leading +
- Handle international format

**Examples**:
- `(555) 123-4567` → `5551234567`
- `+1-555-123-4567` → `+15551234567`

**Priority**: High

### 7.2 Area Code Extraction (FR-PHONE-002)
**Requirement**: The system shall extract area code from US numbers.

**Logic**:
1. Normalize number
2. If starts with +1, extract 3-digit NPA
3. If 10 digits, extract first 3 digits
4. Return null for invalid format

**Priority**: Medium

### 7.3 Display Formatting (FR-PHONE-003)
**Requirement**: The system shall format phone numbers for display.

**Formats**:
- US: `(XXX) XXX-XXXX`
- US+1: `+1 (XXX) XXX-XXXX`
- Raw: Return as-is

**Priority**: Medium

### 7.4 Mobile Detection (FR-PHONE-004)
**Requirement**: The system shall detect mobile numbers.

**Method**: Area code prefix matching
- 1604, 1609, 1614, 1615, 1616, 1617, 1618, 1620, 1622, 1623, 1624

**Note**: This is US-centric; extend for other countries

**Priority**: Medium

---

## 8. Queue Management

### 8.1 Queue Member Control (FR-QUEUE-001)
**Requirement**: The system shall manage queue members.

**Features**:
- Add member to queue (QueueAdd)
- Remove member from queue (QueueRemove)
- Set member priority

**Priority**: Low

### 8.2 Queue Status (FR-QUEUE-002)
**Requirement**: The system shall retrieve queue statistics.

**Data**:
- Queue name
- Number of calls waiting
- Average hold time
- Max wait time

**Priority**: Low

---

## 9. Entity Definitions

### 9.1 AsteriskCall Entity

#### Properties
| Property | Type | Required | Description |
|----------|------|----------|-------------|
| id | int | No | Database ID |
| callerNumber | string | Yes | Calling number |
| calledNumber | string | Yes | Called number |
| direction | string | Yes | inbound/outbound/internal |
| status | string | Yes | ringing/answered/hung_up/missed |
| callStartTime | string | No | ISO datetime |
| callEndTime | string | No | ISO datetime |
| duration | int | No | Seconds |
| linkedContactId | int | No | Linked contact |
| linkedType | string | No | contact/lead/account |
| linkedAccountId | int | No | Linked account |
| userId | int | No | Assigned user |
| uniqueId | string | Yes | Asterisk unique ID |
| channel | string | No | Asterisk channel |
| recordingUrl | string | No | Recording file URL |
| notes | string | No | Call notes |

#### Constants
```php
// Directions
DIRECTION_INBOUND = 'inbound';
DIRECTION_OUTBOUND = 'outbound';
DIRECTION_INTERNAL = 'internal';

// Statuses
STATUS_RINGING = 'ringing';
STATUS_ANSWERED = 'answered';
STATUS_HUNG_UP = 'hung_up';
STATUS_MISSED = 'missed';
STATUS_VOICEMAIL = 'voicemail';

// Link Types
LINKED_NONE = 'none';
LINKED_CONTACT = 'contact';
LINKED_LEAD = 'lead';
LINKED_ACCOUNT = 'account';
```

### 9.2 SMSMessage Entity

#### Properties
| Property | Type | Required | Description |
|----------|------|----------|-------------|
| id | int | No | Database ID |
| fromNumber | string | Yes | Sender number |
| toNumber | string | Yes | Recipient number |
| direction | string | Yes | inbound/outbound |
| message | string | Yes | SMS content |
| status | string | Yes | sent/delivered/failed |
| sentAt | string | No | Send timestamp |
| deliveredAt | string | No | Delivery timestamp |
| contactId | int | No | Linked contact |
| leadId | int | No | Linked lead |
| accountId | int | No | Linked account |
| externalId | string | No | Provider message ID |
| provider | string | Yes | SMS provider |

### 9.3 ClickToCallRequest Entity

#### Properties
| Property | Type | Required | Description |
|----------|------|----------|-------------|
| id | int | No | Database ID |
| userId | int | Yes | Initiating user |
| fromNumber | string | Yes | Source number |
| toNumber | string | Yes | Target number |
| contactId | int | No | Linked contact |
| status | string | Yes | pending/initiated/completed/failed |
| callId | string | No | Asterisk call ID |
| createdAt | string | No | Creation timestamp |
| notes | string | No | Request notes |

---

## 10. Service Interfaces

### 10.1 CallPopupService

```php
class CallPopupService
{
    public function handleNewCall(array $event): ?AsteriskCall;
    public function findMatchingContact(string $phoneNumber): ?array;
    public function showPopup(AsteriskCall $call, ?array $contact = null): array;
    public function initiateClickToCall(int $userId, string $fromNumber, string $toNumber, ?int $contactId = null): ClickToCallRequest;
}
```

### 10.2 SMSService

```php
class SMSService
{
    public function sendSMS(string $from, string $to, string $message): ?SMSMessage;
    public function handleIncomingSMS(array $event): SMSMessage;
    public function linkToContact(SMSMessage $sms, int $contactId): void;
    public function linkToLead(SMSMessage $sms, int $leadId): void;
    public function linkToAccount(SMSMessage $sms, int $accountId): void;
}
```

### 10.3 PhoneNumberMatcher

```php
class PhoneNumberMatcher
{
    public function normalizePhoneNumber(string $phone): string;
    public function extractAreaCode(string $phone): ?string;
    public function formatForDisplay(string $phone, string $format = 'US'): string;
    public function isMobile(string $phone): bool;
}
```

---

## 11. Error Handling

### 11.1 Error Scenarios

| Scenario | Handling |
|----------|----------|
| AMI connection failed | Return false, log error |
| Authentication failed | Throw AsteriskAuthenticationException |
| Command timeout | Return null, log warning |
| Invalid phone number | Throw AsteriskPhoneNumberException |
| Call originate failed | Return null, update request status |

### 11.2 Logging
- All AMI commands logged at DEBUG level
- Connection events logged at INFO level
- Errors logged at ERROR level with context

---

## 12. Non-Functional Requirements

### 12.1 Performance
- AMI command response: < 200ms
- Contact matching: < 100ms
- Phone normalization: < 5ms

### 12.2 Reliability
- Auto-reconnect on connection loss
- Graceful degradation if AMI unavailable
- 99.5% uptime target

### 12.3 Compatibility
- Asterisk 13, 16, 18, 20, 21
- PHP 8.0+
- IPv4 and IPv6

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-13*