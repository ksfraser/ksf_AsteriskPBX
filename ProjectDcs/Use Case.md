# ksf_AsteriskPBX - Use Case

## Document Information
- **Module**: ksf_AsteriskPBX (Asterisk PBX Integration)
- **Version**: 1.0.0
- **Date**: 2026-05-13
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Use Case Overview

### 1.1 Actors
| Actor | Description |
|-------|-------------|
| Sales Agent | Makes outbound calls, receives inbound calls |
| Support Rep | Handles customer support calls |
| Call Center Manager | Monitors queues and recordings |
| System | Automated processes and integrations |

### 1.2 Use Case Categories
- Call Management
- Click-to-Call
- Call Popup
- SMS Messaging
- Contact Matching

---

## 2. Call Management Use Cases

### UC-PBX-001: Connect to Asterisk AMI

**Actor**: System  
**Trigger**: Application startup or reconnection

**Pre-conditions**:
- Asterisk server is reachable
- AMI credentials are configured

**Steps**:
1. Create AsteriskAMI instance with host, port, username, password
2. Call connect() method
3. System establishes TCP socket connection
4. System reads initial greeting (should contain "Asterisk")
5. System sends Login command with credentials
6. System verifies "Authentication accepted" in response
7. Set connected flag to true

**Post-conditions**:
- AMI connection is established and authenticated
- Application can send AMI commands

**Failure Scenarios**:
- F1: Socket connection fails → Return false, log error
- F2: No Asterisk greeting → Disconnect, return false
- F3: Authentication rejected → Throw AuthenticationException

---

### UC-PBX-002: Originate Outbound Call

**Actor**: Sales Agent, System  
**Trigger**: Click-to-call request or automated dialing

**Pre-conditions**:
- AMI connection is active
- Valid channel and extension provided

**Steps**:
1. Receive originate request with channel and extension
2. Generate unique call ID
3. Build Originate AMI command:
   ```
   Action: Originate
   Channel: <channel>
   Context: <context>
   Exten: <extension>
   Priority: 1
   Variable: UNIQUEID=<callId>
   Async: true
   ```
4. Send command to Asterisk
5. Read response
6. If "Originate succeeded" in response, return call ID
7. Otherwise, return null

**Post-conditions**:
- Call initiated or null returned on failure
- Call record created in CRM

**Failure Scenarios**:
- F1: Not connected → Reconnect attempt then fail
- F2: Invalid channel → Return null
- F3: Extension not found → Return null

---

### UC-PBX-003: Hangup Call

**Actor**: Sales Agent, System  
**Trigger**: End call button pressed or call completed

**Pre-conditions**:
- Active call exists
- Channel name is known

**Steps**:
1. Receive hangup request with channel name
2. Build Hangup AMI command
3. Send command to Asterisk
4. Read response
5. Return true if "Hungup" in response

**Post-conditions**:
- Call terminated
- CDR updated with end time

**Failure Scenarios**:
- F1: Channel already hung up → Return true
- F2: Channel not found → Return false

---

### UC-PBX-004: Start Call Recording

**Actor**: System, Call Center Manager  
**Trigger**: Recording button pressed or auto-record enabled

**Pre-conditions**:
- Active call on channel
- Recording filename specified

**Steps**:
1. Receive recording request with channel and filename
2. Build MixMonitor AMI command
3. Send command to Asterisk
4. Read response
5. If "MixMonitor started" in response, return true

**Post-conditions**:
- Recording starts on channel
- Recording URL stored in call record

**Failure Scenarios**:
- F1: Channel not found → Return false
- F2: Recording disabled on channel → Return false

---

### UC-PBX-005: Get Queue Status

**Actor**: Call Center Manager  
**Trigger**: Dashboard refresh or status check

**Pre-conditions**:
- AMI connection active
- Queue name known

**Steps**:
1. Receive queue status request
2. Build QueueStatus AMI command
3. Send command to Asterisk
4. Read and parse response
5. Extract queue statistics from response
6. Return array with queue data

**Post-conditions**:
- Queue statistics returned

**Data Returned**:
```php
[
    'queue' => 'support',
    'calls' => 3,
    'hold_time' => 45.5,
    'max' => 10
]
```

---

## 3. Click-to-Call Use Cases

### UC-CTC-001: Initiate Click-to-Call from Contact

**Actor**: Sales Agent  
**Trigger**: Click on phone number in contact record

**Pre-conditions**:
- Contact has phone number
- User has click-to-call permission
- User has assigned extension

**Steps**:
1. User clicks phone number in CRM
2. System extracts contact phone and user extension
3. Create ClickToCallRequest entity
4. Set status to "pending"
5. Call AsteriskAMI.originateCall()
6. If call succeeds:
   - Update request status to "completed"
   - Set call ID
7. If call fails:
   - Update request status to "failed"

**Post-conditions**:
- Call initiated to contact
- Request status updated
- Call logged in CRM

**UI Flow**:
1. User clicks phone icon next to number
2. System shows "Dialing..." indicator
3. Call connects (softphone rings)
4. Call logged automatically

---

### UC-CTC-002: Initiate Click-to-Call from Opportunity

**Actor**: Sales Agent  
**Trigger**: Click on contact phone in opportunity view

**Pre-conditions**:
- Opportunity has linked contact
- Contact has phone number

**Steps**:
1. User clicks phone number in opportunity
2. System retrieves linked contact
3. Extract phone number
4. Execute click-to-call flow
5. Link call to opportunity

**Post-conditions**:
- Call initiated to opportunity contact
- Call linked to opportunity in CRM

---

## 4. Call Popup Use Cases

### UC-POP-001: Display Popup for Inbound Call

**Actor**: System  
**Trigger**: Newchannel or Dial AMI event received

**Pre-conditions**:
- AMI event listener active
- Incoming call detected

**Steps**:
1. Receive AMI event data
2. Create AsteriskCall entity from event
3. Extract caller number
4. Call PhoneNumberMatcher to normalize
5. Query CRM for contact by phone
6. If contact found:
   - Link call to contact
   - Get contact details (name, account, etc.)
7. Prepare popup data:
   - Format phone for display
   - Determine if mobile
   - Set available actions
8. Return popup data array

**Post-conditions**:
- Popup data ready for UI
- Call linked to contact if matched

**Popup Data Structure**:
```php
[
    'call_id' => 123,
    'unique_id' => 'call_123456',
    'caller_number' => '5551234567',
    'caller_display' => '(555) 123-4567',
    'direction' => 'inbound',
    'status' => 'ringing',
    'contact' => [
        'id' => 1,
        'name' => 'John Doe',
        'company' => 'Acme Corp'
    ],
    'is_mobile' => true,
    'actions' => [
        'answer' => true,
        'dial' => true,
        'sms' => true,
        'voicemail' => true,
        'log' => true
    ]
]
```

---

### UC-POP-002: Display Popup for Unknown Caller

**Actor**: System  
**Trigger**: Inbound call with no matching contact

**Steps**:
1. Receive call event
2. Try to match contact
3. No match found
4. Prepare popup with minimal data:
   - Phone number only
   - "Unknown" name
   - Create new contact option
5. Set popup_type to "unknown"

**Post-conditions**:
- Popup displayed with "Add New Contact" option

---

### UC-POP-003: Update Popup on Call State Change

**Actor**: System  
**Trigger**: Bridge or Hangup AMI event

**Steps**:
1. Receive state change event
2. Find existing popup by unique ID
3. Update call status:
   - If Bridge: Set status to "answered"
   - If Hangup: Set status to "hung_up", calculate duration
4. Update popup display

**Post-conditions**:
- UI reflects current call state

---

## 5. SMS Messaging Use Cases

### UC-SMS-001: Send SMS to Contact

**Actor**: Sales Agent  
**Trigger**: Click SMS button in contact record

**Pre-conditions**:
- Contact has mobile number
- User has SMS permission

**Steps**:
1. User clicks SMS button
2. User enters message text
3. Create SMSMessage entity
4. Call SMSService.sendSMS()
5. AsteriskAMI sends SMS via provider
6. Update SMS status based on response
7. Link SMS to contact
8. Store SMS record

**Post-conditions**:
- SMS sent to contact
- SMS record stored in CRM

**Validation**:
- Detect if number is mobile (not landline)
- Warn if non-mobile number

---

### UC-SMS-002: Handle Incoming SMS

**Actor**: System  
**Trigger**: Incoming SMS event from Asterisk

**Pre-conditions**:
- SMS event listener active

**Steps**:
1. Receive incoming SMS event
2. Create SMSMessage entity
3. Extract from/to/message
4. Normalize phone numbers
5. Try to match sender to contact
6. If match: Link SMS to contact
7. Store SMS record
8. Notify user (if configured)

**Post-conditions**:
- Incoming SMS stored
- Linked to contact if matched

---

### UC-SMS-003: View SMS History

**Actor**: Sales Agent  
**Trigger**: Open SMS tab in contact record

**Steps**:
1. User opens contact record
2. User clicks SMS tab
3. System queries SMS records by contact ID
4. Display SMS history (newest first)
5. Show direction, date, message preview

**Post-conditions**:
- SMS history displayed
- User can click to view full message

---

## 6. Phone Number Intelligence Use Cases

### UC-PHONE-001: Normalize Phone Number

**Actor**: System  
**Trigger**: Any phone number processing

**Steps**:
1. Receive raw phone number
2. Remove all characters except digits and +
3. Preserve leading +
4. Return normalized string

**Examples**:
| Input | Output |
|-------|--------|
| (555) 123-4567 | 5551234567 |
| +1-555-123-4567 | +15551234567 |
| 555.123.4567 | 5551234567 |

---

### UC-PHONE-002: Format Phone for Display

**Actor**: System  
**Trigger**: Display phone number in UI

**Steps**:
1. Receive normalized phone number
2. Determine format (US, US+1, other)
3. Apply formatting pattern
4. Return formatted string

**US Format Output**:
- 10 digits: `(555) 123-4567`
- 11 digits starting with 1: `+1 (555) 123-4567`

---

### UC-PHONE-003: Detect Mobile Number

**Actor**: System  
**Trigger**: Determine if SMS can be sent

**Steps**:
1. Normalize phone number
2. Extract area code (NPA)
3. Compare against mobile prefix list
4. Return boolean

**Mobile Prefixes (US)**:
1604, 1609, 1614, 1615, 1616, 1617, 1618, 1620, 1622, 1623, 1624

---

## 7. Use Case Summary

| UC ID | Use Case | Actor | Priority |
|-------|----------|-------|----------|
| UC-PBX-001 | Connect to Asterisk AMI | System | Critical |
| UC-PBX-002 | Originate Outbound Call | Sales Agent | Critical |
| UC-PBX-003 | Hangup Call | Sales Agent | High |
| UC-PBX-004 | Start Call Recording | System | Medium |
| UC-PBX-005 | Get Queue Status | Manager | Low |
| UC-CTC-001 | Click-to-Call from Contact | Sales Agent | High |
| UC-CTC-002 | Click-to-Call from Opportunity | Sales Agent | High |
| UC-POP-001 | Display Popup for Inbound Call | System | High |
| UC-POP-002 | Display Popup for Unknown Caller | System | Medium |
| UC-POP-003 | Update Popup on State Change | System | High |
| UC-SMS-001 | Send SMS to Contact | Sales Agent | Medium |
| UC-SMS-002 | Handle Incoming SMS | System | Medium |
| UC-SMS-003 | View SMS History | Sales Agent | Low |
| UC-PHONE-001 | Normalize Phone Number | System | High |
| UC-PHONE-002 | Format Phone for Display | System | Medium |
| UC-PHONE-003 | Detect Mobile Number | System | Medium |

---

## 8. Sequence Diagrams

### 8.1 Click-to-Call Sequence
```
Sales Agent     Application     AsteriskAMI     Asterisk     CRM
    |               |               |              |           |
    |--Click Phone->|               |              |           |
    |               |--Extract Numbers             |           |
    |               |--Create Request              |           |
    |               |------------originateCall()-->|           |
    |               |               |---SIP Invite->|           |
    |               |               |<--100 Trying--|           |
    |               |<--Call ID-----|              |           |
    |               |               |              |           |
    |<--Ringing-----|               |              |           |
    |               |               |              |           |
    |<--Connected-->|               |              |           |
    |               |------------originateCall()-->|           |
    |               |               |---ACK-------->|           |
    |               |               |              |           |
    |               |------------->Log Call       |           |
    |               |               |              |      |--->|
    |               |               |              |           |
    |               |               |<--Hangup-----|           |
    |               |------------->Update Status              |
    |               |               |              |           |
```

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-13*