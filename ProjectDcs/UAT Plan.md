# ksf_AsteriskPBX - UAT Plan

## Document Information
- **Module**: ksf_AsteriskPBX (Asterisk PBX Integration)
- **Version**: 1.0.0
- **Date**: 2026-05-13
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Introduction

### 1.1 Purpose
This UAT Plan defines user acceptance tests for the ksf_AsteriskPBX module from an end-user perspective.

### 1.2 Scope
- Click-to-call functionality
- Call popup display
- Contact matching
- SMS messaging
- Phone number display

### 1.3 Test Environment
- **Asterisk**: 18+ with AMI enabled
- **PHP**: 8.0+
- **Browser**: Chrome/Firefox latest
- **Network**: Same LAN as Asterisk

### 1.4 Stakeholders
- Sales Agents
- Support Representatives
- Call Center Managers
- IT Administrators

---

## 2. UAT Test Cases

### 2.1 Click-to-Call (UAT-CTC)

#### UAT-CTC-001: Click-to-Call from Contact List
**Objective**: Verify user can initiate call from contact list

**Test Scenario**:
1. Login to CRM
2. Navigate to Contacts
3. Find a contact with phone number
4. Click phone icon next to number
5. Observe Asterisk initiates call

**Expected Result**: Call initiated to contact's phone

**Acceptance Criteria**:
- [ ] Phone icon is clickable
- [ ] "Dialing..." indicator shown
- [ ] Call connects to correct number
- [ ] Call logged in CRM

---

#### UAT-CTC-002: Click-to-Call from Opportunity
**Objective**: Verify user can call linked contact from opportunity

**Test Scenario**:
1. Open opportunity with linked contact
2. Click call button
3. Observe call to contact's phone

**Expected Result**: Call initiated to opportunity contact

**Acceptance Criteria**:
- [ ] Call button visible on opportunity
- [ ] Correct contact phone used
- [ ] Call linked to opportunity

---

#### UAT-CTC-003: Click-to-Call from Any Phone Field
**Objective**: Verify phone numbers are clickable throughout system

**Test Scenario**:
1. Navigate through CRM modules
2. Find phone number fields
3. Click any phone number

**Expected Result**: Click initiates call

**Acceptance Criteria**:
- [ ] All phone fields are clickable
- [ ] Consistent click behavior
- [ ] Correct number dialed

---

### 2.2 Call Popup (UAT-POP)

#### UAT-POP-001: Popup on Inbound Call
**Objective**: Verify popup displays for incoming calls

**Test Scenario**:
1. Have test phone call the Asterisk DID
2. Observe popup on CRM screen

**Expected Result**: Popup shows caller information

**Acceptance Criteria**:
- [ ] Popup appears within 1 second
- [ ] Caller number displayed
- [ ] Caller name (if matched) shown
- [ ] Quick action buttons visible

---

#### UAT-POP-002: Popup Shows Contact Details
**Objective**: Verify popup includes matched contact details

**Test Scenario**:
1. Call from known contact's phone
2. Verify popup shows contact name, company

**Expected Result**: Contact details in popup

**Acceptance Criteria**:
- [ ] Contact name displayed
- [ ] Company name shown (if available)
- [ ] Recent interactions visible

---

#### UAT-POP-003: Popup for Unknown Caller
**Objective**: Verify popup handles unknown callers

**Test Scenario**:
1. Call from number not in CRM
2. Verify popup shows "Unknown" and offers to add

**Expected Result**: Unknown caller popup with add option

**Acceptance Criteria**:
- [ ] "Unknown" shown as name
- [ ] Add New Contact button visible
- [ ] Number correctly displayed

---

#### UAT-POP-004: Popup Updates on Answer
**Objective**: Verify popup reflects call state changes

**Test Scenario**:
1. Receive inbound call
2. Answer the call
3. Observe popup status update

**Expected Result**: Status changes to "In Progress"

**Acceptance Criteria**:
- [ ] Status updates to "Answered"
- [ ] Duration timer starts (if shown)
- [ ] Hangup button clearly visible

---

#### UAT-POP-005: Popup Actions
**Objective**: Verify action buttons work correctly

**Test Scenario**:
1. Receive call popup
2. Test each action button:
   - Answer
   - Dial (transfer)
   - SMS
   - Voicemail
   - Log

**Expected Result**: Each action works as expected

**Acceptance Criteria**:
- [ ] Answer transfers call to softphone
- [ ] SMS opens message composer
- [ ] Voicemail sends to voicemail
- [ ] Log opens call notes

---

### 2.3 Contact Matching (UAT-MATCH)

#### UAT-MATCH-001: Exact Phone Match
**Objective**: Verify contacts match by exact phone number

**Test Scenario**:
1. Create contact with phone (555) 123-4567
2. Call from 5551234567
3. Verify contact matched

**Expected Result**: Contact matched exactly

**Acceptance Criteria**:
- [ ] Contact found and displayed
- [ ] Match based on canonical phone format

---

#### UAT-MATCH-002: Partial Phone Match
**Objective**: Verify system handles partial matches

**Test Scenario**:
1. Call from phone with extension
2. Verify base number matched

**Expected Result**: Contact matched by base number

**Acceptance Criteria**:
- [ ] Extension stripped for matching
- [ ] Contact correctly identified

---

#### UAT-MATCH-003: Multiple Phone Fields
**Objective**: Verify matching checks all phone fields

**Test Scenario**:
1. Create contact with phone, mobile, other
2. Call from mobile number
3. Verify mobile field matched

**Expected Result**: Mobile field matched

**Acceptance Criteria**:
- [ ] All phone fields checked
- [ ] Mobile field included in search

---

### 2.4 Phone Display (UAT-PHONE)

#### UAT-PHONE-001: Format US Phone Numbers
**Objective**: Verify phones display in standard format

**Test Scenario**:
1. View contacts with various phone formats
2. Check display format

**Expected Result**: Consistent (XXX) XXX-XXXX format

**Acceptance Criteria**:
- [ ] All US numbers formatted consistently
- [ ] 10-digit numbers show parentheses format
- [ ] 11-digit numbers show +1 prefix

---

#### UAT-PHONE-002: Display International Numbers
**Objective**: Verify international numbers display correctly

**Test Scenario**:
1. Create contact with UK number +44 20 7123 4567
2. View contact

**Expected Result**: Number displayed with country code

**Acceptance Criteria**:
- [ ] Country code preserved
- [ ] Number is readable

---

#### UAT-PHONE-003: Mobile Indicator
**Objective**: Verify mobile numbers are visually distinct

**Test Scenario**:
1. View contacts with mobile numbers
2. Observe mobile icon/indicator

**Expected Result**: Mobile clearly indicated

**Acceptance Criteria**:
- [ ] Mobile icon shows
- [ ] Color coding (if configured)
- [ ] SMS button enabled

---

### 2.5 SMS Messaging (UAT-SMS)

#### UAT-SMS-001: Send SMS from Contact
**Objective**: Verify user can send SMS to contact

**Test Scenario**:
1. Open contact with mobile number
2. Click SMS button
3. Enter message
4. Send

**Expected Result**: SMS sent via Asterisk

**Acceptance Criteria**:
- [ ] SMS button enabled for mobile
- [ ] Message composer opens
- [ ] SMS sent successfully
- [ ] Sent to correct number

---

#### UAT-SMS-002: View SMS History
**Objective**: Verify SMS history visible in CRM

**Test Scenario**:
1. Send/receive SMS messages
2. Navigate to contact SMS tab
3. View history

**Expected Result**: SMS history displayed

**Acceptance Criteria**:
- [ ] Sent messages shown
- [ ] Received messages shown
- [ ] Chronological order
- [ ] Full message text accessible

---

#### UAT-SMS-003: SMS to Non-Mobile Warning
**Objective**: Verify warning for SMS to landline

**Test Scenario**:
1. Try to send SMS to landline number
2. Observe warning

**Expected Result**: User warned before sending

**Acceptance Criteria**:
- [ ] Warning message displayed
- [ ] Option to proceed or cancel
- [ ] Clear indication number is landline

---

### 2.6 Call Recording (UAT-REC)

#### UAT-REC-001: Start Recording During Call
**Objective**: Verify user can start recording

**Test Scenario**:
1. During active call
2. Click Record button
3. Verify recording starts

**Expected Result**: Recording initiated

**Acceptance Criteria**:
- [ ] Record button visible during call
- [ ] Recording indicator shown
- [ ] Asterisk MixMonitor starts

---

#### UAT-REC-002: Stop Recording
**Objective**: Verify user can stop recording

**Test Scenario**:
1. During recording
2. Click Stop Recording
3. Verify recording stops

**Expected Result**: Recording stopped

**Acceptance Criteria**:
- [ ] Stop button works
- [ ] Recording saved
- [ ] Duration recorded correctly

---

#### UAT-REC-003: Access Recording
**Objective**: Verify recordings accessible later

**Test Scenario**:
1. After call ends
2. Navigate to call record
3. Access recording

**Expected Result**: Recording playable

**Acceptance Criteria**:
- [ ] Recording link present
- [ ] Playback works
- [ ] Download option available

---

### 2.7 Administration (UAT-ADMIN)

#### UAT-ADMIN-001: Configure AMI Connection
**Objective**: Verify admin can configure Asterisk connection

**Test Scenario**:
1. Navigate to Asterisk settings
2. Enter host, port, credentials
3. Test connection

**Expected Result**: Connection successful

**Acceptance Criteria**:
- [ ] Settings form complete
- [ ] Connection test works
- [ ] Credentials saved securely

---

#### UAT-ADMIN-002: View Connection Status
**Objective**: Verify connection status visible

**Test Scenario**:
1. Check Asterisk status in admin panel
2. Verify connected status shown

**Expected Result**: Status displayed correctly

**Acceptance Criteria**:
- [ ] Connection status visible
- [ ] Last connected time shown
- [ ] Error messages logged

---

## 3. Sign-Off Criteria

### 3.1 Test Completion Metrics
- **Total UAT Test Cases**: 20
- **Passed**: [ ]
- **Failed**: [ ]
- **Blocked**: [ ]
- **Pass Rate**: [ ]%

### 3.2 Critical Path Tests (Must Pass)
- [ ] Click-to-call from contact
- [ ] Call popup display
- [ ] Contact matching
- [ ] SMS to mobile
- [ ] AMI connection

### 3.3 Sign-Off Table
| Test Area | Tester | Date | Result |
|-----------|--------|------|--------|
| Click-to-Call | | | Pass/Fail |
| Call Popup | | | Pass/Fail |
| Contact Matching | | | Pass/Fail |
| Phone Display | | | Pass/Fail |
| SMS Messaging | | | Pass/Fail |
| Call Recording | | | Pass/Fail |
| Administration | | | Pass/Fail |

---

## 4. Defect Reporting

### 4.1 Severity Levels
- **Critical**: Call functionality broken
- **High**: Feature not working as specified
- **Medium**: Feature partially working
- **Low**: Cosmetic issue

### 4.2 Defect Report Template
```
ID: [Number]
Test Case: [UAT-XXX-###]
Environment: [Details]
Expected: [What should happen]
Actual: [What happened]
Severity: [Critical/High/Medium/Low]
Priority: [P0/P1/P2/P3]
Tester: [Name]
Date: [Date]
```

---

## 5. Success Criteria

### 5.1 Go/No-Go Decision
Module passes UAT when:
1. 100% critical test cases pass
2. 90% overall test cases pass
3. No Critical defects open
4. Business sign-off obtained

### 5.2 Issue Resolution
| Severity | Resolution |
|----------|------------|
| Critical | Must fix before release |
| High | Should fix before release |
| Medium | Release OK with known issues |
| Low | Can defer to next release |

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-13*