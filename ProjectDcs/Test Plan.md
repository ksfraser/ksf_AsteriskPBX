# ksf_AsteriskPBX - Test Plan

## Document Information
- **Module**: ksf_AsteriskPBX (Asterisk PBX Integration)
- **Version**: 1.0.0
- **Date**: 2026-05-13
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Introduction

### 1.1 Purpose
This test plan defines the testing strategy for the ksf_AsteriskPBX module, ensuring all functional requirements are met and the module operates correctly with Asterisk systems.

### 1.2 Scope
- Unit testing of entity classes
- Unit testing of service classes
- AMI command testing
- Phone number intelligence testing

### 1.3 Test Environment
- **PHP Version**: 8.0+
- **Testing Framework**: PHPUnit
- **Asterisk Version**: 13+ (for integration tests)

---

## 2. Testing Strategy

### 2.1 Test Levels

| Level | Description | Coverage Target |
|-------|-------------|-----------------|
| Unit | Individual class methods | 100% |
| Integration | AMI communication | Core methods |
| System | End-to-end scenarios | Key flows |

### 2.2 Test Types

| Type | Description |
|------|-------------|
| Functional | Verify features work as specified |
| Regression | Ensure no existing features broken |
| Edge Cases | Invalid input, boundary conditions |

---

## 3. Test Cases

### 3.1 AsteriskCall Entity (TC-CALL)

#### TC-CALL-001: Create AsteriskCall Instance
**Preconditions**: None  
**Test Steps**:
1. Instantiate new AsteriskCall
2. Verify object created

**Expected Result**: Object is instance of AsteriskCall

**Priority**: High

---

#### TC-CALL-002: Set and Get Caller Number
**Preconditions**: AsteriskCall instance created  
**Test Steps**:
1. Call setCallerNumber('+15551234567')
2. Call getCallerNumber()
3. Verify value matches

**Expected Result**: Returns '+15551234567'

**Priority**: High

---

#### TC-CALL-003: Set and Get Called Number
**Preconditions**: AsteriskCall instance created  
**Test Steps**:
1. Call setCalledNumber('+15559876543')
2. Call getCalledNumber()

**Expected Result**: Returns '+15559876543'

**Priority**: High

---

#### TC-CALL-004: Set and Get Direction
**Preconditions**: AsteriskCall instance created  
**Test Steps**:
1. Call setDirection(AsteriskCall::DIRECTION_INBOUND)
2. Call getDirection()

**Expected Result**: Returns 'inbound'

**Priority**: High

---

#### TC-CALL-005: Check isInbound Method
**Preconditions**: AsteriskCall instance created  
**Test Steps**:
1. Set direction to inbound
2. Call isInbound()
3. Change direction to outbound
4. Call isInbound()

**Expected Result**: True for inbound, false for outbound

**Priority**: High

---

#### TC-CALL-006: Link to Contact
**Preconditions**: AsteriskCall instance created  
**Test Steps**:
1. Call linkToContact(5)
2. Verify getLinkedContactId() returns 5
3. Verify getLinkedType() returns 'contact'

**Expected Result**: Contact linked correctly

**Priority**: High

---

#### TC-CALL-007: Call Status Methods
**Preconditions**: AsteriskCall instance created  
**Test Steps**:
1. Call answer()
2. Verify getStatus() returns 'answered'
3. Call hangup()
4. Verify getStatus() returns 'hung_up'

**Expected Result**: Status transitions work

**Priority**: Medium

---

### 3.2 SMSMessage Entity (TC-SMS)

#### TC-SMS-001: Create SMSMessage Instance
**Preconditions**: None  
**Test Steps**:
1. Instantiate new SMSMessage
2. Verify object created

**Expected Result**: Object is instance of SMSMessage

**Priority**: High

---

#### TC-SMS-002: Set SMS Properties
**Preconditions**: SMSMessage instance created  
**Test Steps**:
1. Set from number
2. Set to number
3. Set message
4. Set direction

**Expected Result**: All properties set correctly

**Priority**: High

---

#### TC-SMS-003: Check isInbound Method
**Preconditions**: SMSMessage instance created  
**Test Steps**:
1. Set direction to inbound
2. Call isInbound()
3. Change to outbound
4. Call isInbound()

**Expected Result**: Returns correct boolean

**Priority**: Medium

---

### 3.3 PhoneNumberMatcher Service (TC-PHONE)

#### TC-PHONE-001: Normalize Standard US Number
**Preconditions**: PhoneNumberMatcher instance created  
**Test Steps**:
1. Call normalizePhoneNumber('(555) 123-4567')
2. Call normalizePhoneNumber('555.123.4567')
3. Call normalizePhoneNumber('555-123-4567')

**Expected Result**: All return '5551234567'

**Priority**: High

---

#### TC-PHONE-002: Normalize International Number
**Preconditions**: PhoneNumberMatcher instance created  
**Test Steps**:
1. Call normalizePhoneNumber('+1-555-123-4567')
2. Call normalizePhoneNumber('+44 20 7123 4567')

**Expected Result**: '+15551234567' and '+442071234567'

**Priority**: High

---

#### TC-PHONE-003: Format US Number for Display
**Preconditions**: PhoneNumberMatcher instance created  
**Test Steps**:
1. Call formatForDisplay('5551234567')
2. Call formatForDisplay('15551234567')

**Expected Result**: '(555) 123-4567' and '+1 (555) 123-4567'

**Priority**: High

---

#### TC-PHONE-004: Extract Area Code
**Preconditions**: PhoneNumberMatcher instance created  
**Test Steps**:
1. Call extractAreaCode('+15551234567')
2. Call extractAreaCode('5551234567')

**Expected Result**: Both return '555'

**Priority**: Medium

---

#### TC-PHONE-005: Detect Mobile Number - Mobile
**Preconditions**: PhoneNumberMatcher instance created  
**Test Steps**:
1. Call isMobile('16091234567')
2. Call isMobile('16151234567')

**Expected Result**: Both return true

**Priority**: High

---

#### TC-PHONE-006: Detect Mobile Number - Landline
**Preconditions**: PhoneNumberMatcher instance created  
**Test Steps**:
1. Call isMobile('2125551234')
2. Call isMobile('3125551234')

**Expected Result**: Both return false

**Priority**: High

---

### 3.4 ClickToCallRequest Entity (TC-CTC)

#### TC-CTC-001: Initiate Click-to-Call Request
**Preconditions**: ClickToCallRequest instance created  
**Test Steps**:
1. Set user ID, from number, to number
2. Call initiate()
3. Verify status is 'initiated'
4. Verify createdAt is set

**Expected Result**: Status updated, timestamp set

**Priority**: High

---

#### TC-CTC-002: Complete Click-to-Call Request
**Preconditions**: ClickToCallRequest in initiated state  
**Test Steps**:
1. Call complete('call_abc123')
2. Verify status is 'completed'
3. Verify callId is 'call_abc123'

**Expected Result**: Request marked complete

**Priority**: High

---

#### TC-CTC-003: Fail Click-to-Call Request
**Preconditions**: ClickToCallRequest instance created  
**Test Steps**:
1. Call initiate()
2. Call fail()
3. Verify status is 'failed'

**Expected Result**: Request marked failed

**Priority**: High

---

### 3.5 AsteriskAMI Integration (TC-AMI)

#### TC-AMI-001: Connection Failure Handling
**Preconditions**: Invalid host/port  
**Test Steps**:
1. Create AsteriskAMI with invalid host
2. Call connect()
3. Verify returns false

**Expected Result**: Returns false, no exception thrown

**Priority**: High

---

#### TC-AMI-002: Command Timeout
**Preconditions**: None (mock test)  
**Test Steps**:
1. Mock socket that doesn't respond
2. Send command
3. Verify timeout handling

**Expected Result**: Returns null after timeout

**Priority**: Medium

---

### 3.6 CallPopupService (TC-POP)

#### TC-POP-001: Handle New Call Event
**Preconditions**: CallPopupService instance created  
**Test Steps**:
1. Create mock event data
2. Call handleNewCall(event)
3. Verify AsteriskCall created with correct data

**Expected Result**: Call entity with correct properties

**Priority**: High

---

#### TC-POP-002: Show Popup with Contact
**Preconditions**: AsteriskCall with contact linked  
**Test Steps**:
1. Create call with caller number
2. Create mock contact data
3. Call showPopup(call, contact)
4. Verify popup data structure

**Expected Result**: Correct popup data with actions

**Priority**: High

---

#### TC-POP-003: Show Popup Without Contact
**Preconditions**: AsteriskCall without contact  
**Test Steps**:
1. Create call with unknown number
2. Call showPopup(call, null)
3. Verify popup_type is 'unknown'

**Expected Result**: Unknown caller popup

**Priority**: Medium

---

### 3.7 SMSService (TC-SMS-SVC)

#### TC-SMS-SVC-001: Send SMS
**Preconditions**: SMSService instance with mock AMI  
**Test Steps**:
1. Call sendSMS('+15551234567', '+15559876543', 'Hello')
2. Verify SMSMessage created
3. Verify direction is outbound

**Expected Result**: SMS sent, entity created

**Priority**: High

---

#### TC-SMS-SVC-002: Handle Incoming SMS
**Preconditions**: SMSService instance  
**Test Steps**:
1. Create mock incoming SMS event
2. Call handleIncomingSMS(event)
3. Verify SMSMessage created with correct data

**Expected Result**: Inbound SMS entity created

**Priority**: High

---

## 4. Performance Tests

### 4.1 Phone Number Processing
| Test | Input Size | Target Time |
|------|------------|-------------|
| Normalize | 10,000 numbers | < 100ms |
| Format | 10,000 numbers | < 200ms |
| Mobile Detection | 10,000 numbers | < 150ms |

### 4.2 Contact Matching
| Test | Database Size | Target Time |
|------|---------------|-------------|
| Exact Match | 10,000 contacts | < 50ms |
| Partial Match | 10,000 contacts | < 100ms |

---

## 5. Security Tests

### 5.1 Input Validation
| Test | Input | Expected |
|------|-------|----------|
| SQL Injection | `'; DROP TABLE users; --` | Escaped/filtered |
| XSS | `<script>alert(1)</script>` | Escaped |
| Invalid Phone | `abc123` | Normalize handles gracefully |

### 5.2 AMI Command Safety
| Test | Input | Expected |
|------|-------|----------|
| Command Injection | `Channel: SIP\nAction: Logoff` | Parsed as single action |
| Newline Injection | `Digits: 1\nDigits: 2` | Sent as single DTMF |

---

## 6. Test Data

### 6.1 Sample Phone Numbers
```php
$testNumbers = [
    'us_landline' => '2125551234',
    'us_mobile' => '16091234567',
    'us_formatted' => '(555) 123-4567',
    'us_international' => '+1-555-123-4567',
    'uk_number' => '+442071234567',
    'invalid' => 'abc123xyz',
];
```

### 6.2 Sample AMI Events
```php
$mockEvent = [
    'Event' => 'Newchannel',
    'UniqueID' => '1234567890.1',
    'CallerIDNum' => '5551234567',
    'DestNum' => '1000',
    'Channel' => 'SIP/5551234567-00000001',
];
```

---

## 7. Test Execution

### 7.1 Run Commands
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test class
./vendor/bin/phpunit tests/Unit/AsteriskCallTest.php

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/
```

### 7.2 Pass/Fail Criteria
| Test Type | Required Pass Rate |
|-----------|-------------------|
| Unit Tests | 100% |
| Integration Tests | 95% |
| Critical Path | 100% |

---

## 8. Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| AMI version differences | Medium | Medium | Version detection |
| Network issues | Medium | Medium | Timeout handling |
| Phone format variations | High | Low | Comprehensive parsing |

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-13*